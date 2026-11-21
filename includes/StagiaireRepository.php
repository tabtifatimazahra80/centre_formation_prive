<?php
declare(strict_types=1);

/**
 * Accès à la base pour les étudiants (tables utilisateur + stagiaire).
 * Toutes les requêtes sont préparées.
 */
final class StagiaireRepository
{
    private const SELECT = 'SELECT u.idUtilisateur AS id, s.numInscription AS matricule,
                                   u.nom, u.prenom, u.email, u.telephone, u.statut,
                                   s.dateNaissance AS date_naissance
                            FROM stagiaire s
                            INNER JOIN utilisateur u ON u.idUtilisateur = s.idUtilisateur';

    public function __construct(private PDO $pdo)
    {
    }

    /** Étudiants correspondant à la recherche (nom, prénom, matricule) et au statut. */
    public function rechercher(string $recherche, string $statut, int $limite, int $decalage): array
    {
        [$where, $parametres] = $this->filtres($recherche, $statut);

        $stmt = $this->pdo->prepare(
            self::SELECT . $where . ' ORDER BY s.numInscription LIMIT :limite OFFSET :decalage'
        );
        foreach ($parametres as $cle => $valeur) {
            $stmt->bindValue($cle, $valeur);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':decalage', $decalage, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function compter(string $recherche, string $statut): int
    {
        [$where, $parametres] = $this->filtres($recherche, $statut);

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM stagiaire s
             INNER JOIN utilisateur u ON u.idUtilisateur = s.idUtilisateur' . $where
        );
        $stmt->execute($parametres);

        return (int) $stmt->fetchColumn();
    }

    public function trouver(int $id): ?array
    {
        $stmt = $this->pdo->prepare(self::SELECT . ' WHERE u.idUtilisateur = :id');
        $stmt->execute([':id' => $id]);
        $etudiant = $stmt->fetch();

        return $etudiant === false ? null : $etudiant;
    }

    public function matriculeExiste(string $matricule, ?int $idExclu = null): bool
    {
        $sql = 'SELECT 1 FROM stagiaire WHERE numInscription = :matricule';
        $parametres = [':matricule' => $matricule];

        if ($idExclu !== null) {
            $sql .= ' AND idUtilisateur <> :id';
            $parametres[':id'] = $idExclu;
        }

        $stmt = $this->pdo->prepare($sql . ' LIMIT 1');
        $stmt->execute($parametres);

        return $stmt->fetchColumn() !== false;
    }

    public function emailExiste(string $email, ?int $idExclu = null): bool
    {
        $sql = 'SELECT 1 FROM utilisateur WHERE email = :email';
        $parametres = [':email' => $email];

        if ($idExclu !== null) {
            $sql .= ' AND idUtilisateur <> :id';
            $parametres[':id'] = $idExclu;
        }

        $stmt = $this->pdo->prepare($sql . ' LIMIT 1');
        $stmt->execute($parametres);

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Crée l'utilisateur (rôle stagiaire, mot de passe par défaut haché)
     * puis la ligne stagiaire, sans groupe. Retourne l'id créé.
     */
    public function creer(array $d): int
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO utilisateur (nom, prenom, email, telephone, motDePasse, statut, idRole)
                 VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe, :statut, :id_role)'
            );
            $stmt->execute([
                ':nom'          => $d['nom'],
                ':prenom'       => $d['prenom'],
                ':email'        => $d['email'],
                ':telephone'    => $d['telephone'],
                ':mot_de_passe' => password_hash(MOT_DE_PASSE_PAR_DEFAUT, PASSWORD_DEFAULT),
                ':statut'       => $d['statut'],
                ':id_role'      => $this->idRole('stagiaire'),
            ]);
            $id = (int) $this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare(
                'INSERT INTO stagiaire (idUtilisateur, numInscription, dateNaissance, idGroupe)
                 VALUES (:id, :matricule, :date_naissance, NULL)'
            );
            $stmt->execute([
                ':id'             => $id,
                ':matricule'      => $d['matricule'],
                ':date_naissance' => $d['date_naissance'],
            ]);

            $this->pdo->commit();

            return $id;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function modifier(int $id, array $d): void
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'UPDATE utilisateur
                 SET nom = :nom, prenom = :prenom, email = :email,
                     telephone = :telephone, statut = :statut
                 WHERE idUtilisateur = :id'
            );
            $stmt->execute([
                ':nom'       => $d['nom'],
                ':prenom'    => $d['prenom'],
                ':email'     => $d['email'],
                ':telephone' => $d['telephone'],
                ':statut'    => $d['statut'],
                ':id'        => $id,
            ]);

            $stmt = $this->pdo->prepare(
                'UPDATE stagiaire
                 SET numInscription = :matricule, dateNaissance = :date_naissance
                 WHERE idUtilisateur = :id'
            );
            $stmt->execute([
                ':matricule'      => $d['matricule'],
                ':date_naissance' => $d['date_naissance'],
                ':id'             => $id,
            ]);

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /** Passe l'étudiant à « inactif » (sans le supprimer). */
    public function desactiver(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE utilisateur u
             INNER JOIN stagiaire s ON s.idUtilisateur = u.idUtilisateur
             SET u.statut = 'inactif'
             WHERE u.idUtilisateur = :id AND u.statut = 'actif'"
        );
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }

    private function idRole(string $libelle): int
    {
        $stmt = $this->pdo->prepare('SELECT idRole FROM role WHERE libelle = :libelle');
        $stmt->execute([':libelle' => $libelle]);
        $id = $stmt->fetchColumn();

        if ($id === false) {
            throw new RuntimeException("Rôle introuvable : $libelle");
        }

        return (int) $id;
    }

    /** Construit la clause WHERE et ses paramètres pour la recherche. */
    private function filtres(string $recherche, string $statut): array
    {
        $conditions = [];
        $parametres = [];

        if ($recherche !== '') {
            $motif = '%' . addcslashes($recherche, '%_\\') . '%';
            $conditions[] = '(u.nom LIKE :r1 OR u.prenom LIKE :r2 OR s.numInscription LIKE :r3)';
            $parametres[':r1'] = $motif;
            $parametres[':r2'] = $motif;
            $parametres[':r3'] = $motif;
        }

        if ($statut !== 'tous') {
            $conditions[] = 'u.statut = :statut';
            $parametres[':statut'] = $statut;
        }

        $where = $conditions === [] ? '' : ' WHERE ' . implode(' AND ', $conditions);

        return [$where, $parametres];
    }
}
