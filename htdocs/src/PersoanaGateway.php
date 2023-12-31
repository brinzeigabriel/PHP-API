<?php

// Aceasta clasa ofera posibilitatea de a gestiona datele 
// despre persoanele dintr-o baza de date
class PersoanaGateway
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        // se stabileste o conexiune la baza de date.
        $this->conn = $database->getConnection();
    }

    //obtinerea tuturor informatiilor din tabela persoana
    public function getAll(): array
    {
        $sql = "SELECT *
                FROM persoana
                Order BY id";
        
        $stmt = $this->conn->query($sql); // nu folosim prepare fiindca nu avem stringuri
                                          // de introdus in comanda

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //obtinerea tuturor informatiilor din tabela persoana pentru id-ul introdus
    // GET /api/persoana/:id
    public function getById(string $id): array | false
    {
        $sql = "SELECT *
                FROM persoana
                WHERE id = :id";
        //prepare este pentru executarea securizata a comenzii sql
        $stmt = $this->conn->prepare($sql); // + avoiding sql injection

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        //obtinerea rezultatului unei interogari sql sub forma de vector asociat
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //crearea unei inregistrari ( persoane ) noi in DB
    // POST /api/pesoana
    public function create(array $data): string
    {
        // Verificam daca CNP exista deja in baza de date din moment ce este coloana unica
        $cnp = $data["cnp"];
        $checkSql = "SELECT COUNT(*) FROM persoana WHERE CNP = :cnp";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->bindValue(":cnp", $cnp, PDO::PARAM_STR);
        $checkStmt->execute();

        $existingCount = $checkStmt->fetchColumn(); //preluare coloana cu datele respective

        //daca exista acel cnp in db vom intoarce -1
        if ($existingCount > 0) {
            return -1;
        } else {
            $sql = "INSERT INTO persoana (CNP, Nume, Prenume, Oras, Tara, Data_de_nastere)
                    VALUES (:cnp, :nume, :prenume, :oras, :tara, :data_nasterii)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":cnp", $data["cnp"], PDO::PARAM_STR);
            $stmt->bindValue(":nume", $data["nume"], PDO::PARAM_STR);
            $stmt->bindValue(":prenume", $data["prenume"], PDO::PARAM_STR);

            if(empty($data["oras"])){
                $stmt->bindValue(":oras", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":oras", $data["oras"], PDO::PARAM_STR);
            }

            if(empty($data["tara"])){
                $stmt->bindValue(":tara", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":tara", $data["tara"], PDO::PARAM_STR);
            }
            
            $stmt->bindValue(":data_nasterii", $data["data_nasterii"], PDO::PARAM_STR);

            $stmt->execute();
            
            return $this->conn->lastInsertId();// intoarce id-ul pentru persoana introdusa
        }
    }

    //actualizarea unei inregistrari (persoane) in DB 
    // PATCH /persoana/:id
    public function update(string $id, array $data): int
    {
        $fields = []; //campurile coloanelor din baza de date

        //binding all data to $fields
        if(!empty($data["cnp"]))
        {
        $fields["cnp"] = [
            $data["cnp"],
            PDO::PARAM_STR
        ];
        }

        if(!empty($data["nume"]))
        {
        $fields["nume"] = [
            $data["nume"],
            PDO::PARAM_STR
        ];
        }

        if(!empty($data["prenume"]))
        {
        $fields["prenume"] = [
            $data["prenume"],
            PDO::PARAM_STR
        ];
        }

        if(!empty($data["oras"]))
        {
        $fields["oras"] = [
            $data["oras"],
            PDO::PARAM_STR
        ];
        }
        
        if(!empty($data["tara"]))
        {
        $fields["tara"] = [
            $data["tara"],
            PDO::PARAM_STR
        ];
        }

        if(!empty($data["data_nasterii"]))
        {
        $fields["data_de_nastere"] = [
            $data["data_nasterii"],
            PDO::PARAM_STR
        ];
        }

        if(empty($fields)){
            return 0;
        }else{
            // folosit pentru a actualiza valoarea campului specificat
            // pentru fiecare field
            $sets = array_map(function($value) {
                return "$value = :$value";
            },array_keys($fields));
    
            // implode = split sets array + ","
            $sql = "UPDATE persoana "
                    ."SET ".implode(",",$sets)
                    ." WHERE id = :id";
    
            $stmt = $this->conn->prepare($sql);                    
            
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            
            foreach($fields as $name => $values){
                $stmt->bindValue(":$name",$values[0],$values[1]);
            }

            $stmt->execute();

            return $stmt->rowCount();
        }
    }

    //stergerea unei inregistrari (persoane) din DB
    // DELETE /api/persoana/:id
    public function delete(string $id): int
    {
        $sql = "DELETE FROM persoana
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);                    
        
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}