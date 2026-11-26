<?php

class SeanceRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Requête commune pour récupérer les informations d'une séance
    private function requeteSeances(): string
    {
        return "
            SELECT
                s.idSeance,
                s.dateSeance,
                s.typeSeance,
                s.idGroupe,
                s.idModule,
                s.idFormateur,

                g.codeGroupe,
                g.nom AS nomGroupe,

                m.codeModule,
                m.intitule AS nomModule,

                u.nom AS nomFormateur,
                u.prenom AS prenomFormateur

            FROM seance s

            INNER JOIN groupe g
                ON s.idGroupe = g.idGroupe

            INNER JOIN module m
                ON s.idModule = m.idModule

            INNER JOIN formateur f
                ON s.idFormateur = f.idUtilisateur

            INNER JOIN utilisateur u
                ON f.idUtilisateur = u.idUtilisateur
        ";
    }


    // Afficher toutes les séances
    public function lister(): array
    {
        $sql = $this->requeteSeances() . "
            ORDER BY s.dateSeance DESC
        ";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Trouver une séance par son ID
    public function trouver(int $id): ?array
    {
        $sql = $this->requeteSeances() . "
            WHERE s.idSeance = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $seance = $stmt->fetch(PDO::FETCH_ASSOC);

        return $seance ?: null;
    }


    // Ajouter une séance
    public function creer(array $donnees): bool
    {
        $sql = "
            INSERT INTO seance
            (
                dateSeance,
                typeSeance,
                idGroupe,
                idModule,
                idFormateur
            )
            VALUES
            (
                :dateSeance,
                :typeSeance,
                :idGroupe,
                :idModule,
                :idFormateur
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':dateSeance'  => $donnees['dateSeance'],
            ':typeSeance'  => $donnees['typeSeance'],
            ':idGroupe'    => $donnees['idGroupe'],
            ':idModule'    => $donnees['idModule'],
            ':idFormateur' => $donnees['idFormateur']
        ]);
    }


    // Modifier une séance
    public function modifier(int $id, array $donnees): bool
    {
        $sql = "
            UPDATE seance
            SET
                dateSeance = :dateSeance,
                typeSeance = :typeSeance,
                idGroupe = :idGroupe,
                idModule = :idModule,
                idFormateur = :idFormateur

            WHERE idSeance = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':dateSeance'  => $donnees['dateSeance'],
            ':typeSeance'  => $donnees['typeSeance'],
            ':idGroupe'    => $donnees['idGroupe'],
            ':idModule'    => $donnees['idModule'],
            ':idFormateur' => $donnees['idFormateur'],
            ':id'          => $id
        ]);
    }


    // Supprimer une séance
    public function supprimer(int $id): bool
    {
        $sql = "
            DELETE FROM seance
            WHERE idSeance = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }


    // Récupérer les groupes actifs
    public function groupesActifs(): array
    {
        $sql = "
            SELECT
                idGroupe,
                codeGroupe,
                nom

            FROM groupe

            WHERE statut = 'actif'

            ORDER BY codeGroupe ASC
        ";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Récupérer les modules disponibles
    public function modulesDisponibles(): array
    {
        $sql = "
            SELECT
                idModule,
                codeModule,
                intitule

            FROM module

            ORDER BY codeModule ASC
        ";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Récupérer les formateurs actifs
    public function formateursActifs(): array
    {
        $sql = "
            SELECT
                f.idUtilisateur,
                u.nom,
                u.prenom

            FROM formateur f

            INNER JOIN utilisateur u
                ON f.idUtilisateur = u.idUtilisateur

            WHERE u.statut = 'actif'

            ORDER BY u.nom ASC
        ";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Récupérer les étudiants d'une séance avec leur présence
    public function etudiantsParSeance(int $idSeance): array
    {
        $sql = "
            SELECT
                u.idUtilisateur,
                u.nom,
                u.prenom,
                u.email,
                st.numInscription,
                p.statutPresence

            FROM seance se

            INNER JOIN stagiaire st
                ON st.idGroupe = se.idGroupe

            INNER JOIN utilisateur u
                ON u.idUtilisateur = st.idUtilisateur

            LEFT JOIN presence p
                ON p.idStagiaire = st.idUtilisateur
                AND p.idSeance = se.idSeance

            WHERE se.idSeance = :idSeance

            ORDER BY u.nom ASC, u.prenom ASC
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':idSeance' => $idSeance
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}