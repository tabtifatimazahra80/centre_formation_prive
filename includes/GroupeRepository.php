<?php
declare(strict_types=1);

/** Accès à la base pour les groupes (table groupe). Requêtes préparées. */
final class GroupeRepository
{
    private const SELECT = 'SELECT idGroupe AS id, codeGroupe AS code, nom, filiere, annee, capacite, statut
                            FROM groupe';

    public function __construct(private PDO $pdo)
    {
    }

    /** Tous les groupes, triés par code. */
    public function lister(): array
    {
        return $this->pdo->query(self::SELECT . ' ORDER BY codeGroupe')->fetchAll();
    }

    public function trouver(int $id): ?array
    {
        $stmt = $this->pdo->prepare(self::SELECT . ' WHERE idGroupe = :id');
        $stmt->execute([':id' => $id]);
        $groupe = $stmt->fetch();

        return $groupe === false ? null : $groupe;
    }

    public function codeExiste(string $code, ?int $idExclu = null): bool
    {
        $sql = 'SELECT 1 FROM groupe WHERE codeGroupe = :code';
        $parametres = [':code' => $code];

        if ($idExclu !== null) {
            $sql .= ' AND idGroupe <> :id';
            $parametres[':id'] = $idExclu;
        }

        $stmt = $this->pdo->prepare($sql . ' LIMIT 1');
        $stmt->execute($parametres);

        return $stmt->fetchColumn() !== false;
    }

    public function creer(array $d): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO groupe (codeGroupe, nom, filiere, annee, capacite, statut)
             VALUES (:code, :nom, :filiere, :annee, :capacite, :statut)'
        );
        $stmt->execute([
            ':code'     => $d['code'],
            ':nom'      => $d['nom'],
            ':filiere'  => $d['filiere'],
            ':annee'    => $d['annee'],
            ':capacite' => $d['capacite'],
            ':statut'   => $d['statut'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function modifier(int $id, array $d): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE groupe
             SET codeGroupe = :code, nom = :nom, filiere = :filiere,
                 annee = :annee, capacite = :capacite, statut = :statut
             WHERE idGroupe = :id'
        );
        $stmt->execute([
            ':code'     => $d['code'],
            ':nom'      => $d['nom'],
            ':filiere'  => $d['filiere'],
            ':annee'    => $d['annee'],
            ':capacite' => $d['capacite'],
            ':statut'   => $d['statut'],
            ':id'       => $id,
        ]);
    }
}
