<?php
// Routes

$app->get("/", function ($request, $response) {

    return $this->response->write(
        "<h1>RESTful API Slim</h1>
<a href ='doc/index.html'>API Documentation</a>");

});

// get all sites
$app->get('/sites', function ($request, $response) {
$result = new Result();
    try {
        $dbquery = $this->db->prepare("SELECT * FROM " . TABLE);
        $dbquery->execute();
        $sites = $dbquery->fetchAll();
        $result->setCode(TRUE);
        $result->setStatus(OK);
        $result->setSites($sites);
    } catch (PDOException $e) {
        $result->setCode(FALSE);
        $result->setStatus(CONFLICT);
        $result->setMessage("Error:oooo " . $e->getMessage());
    }
    return $this->response->withJson($result);
});

$app->get('/site/[{id}]', function ($request, $response, $args) {
    $result = new Result();
    try {

        $dbquery = $this->db->prepare("SELECT * FROM " . TABLE . " WHERE id = ?");
        $dbquery->bindParam(1, $args['id']);
        $dbquery->execute();
        $site = $dbquery->fetchObject();
        if ($site != null) {
            $result->setCode(TRUE);
            $result->setStatus(OK);
            $result->setSites($site);
        }
        else {
            $result->setCode(FALSE);
            $result->setStatus(NOT_COMPLETED);
            $result->setMessage("Does the site exist?");
        }
    } catch (PDOException $e) {
        $result->setCode(FALSE);
        $result->setStatus(CONFLICT);
        $result->setMessage("Error: " . $e->getMessage());
    }
    return $this->response->withJson($result);
});


$app->post('/site/mail', function ($request, $response){

    $result = new Result();
    $input = $request->getParsedBody();


    $this->mail->IsSMTP();

    try {
        $this->mail->SMTPDebug = 0;
        $this->mail->SMTPAuth = true;

        //$mail->SMTPSecure = "tls";
        $this->mail->SMTPSecure = "ssl";
        $this->mail->Host = "smtp.gmail.com";
        //$mail->Host = "smtp.openmailbox.org";
        //$mail->Port = 587;
        $this->mail->Port = 465;

        $this->mail->Username = $input['from'];
        $this->mail->Password = $input['pwd'];
        $this->mail->AddAddress($input['to']);
        $this->mail->SetFrom($input['from'], 'Ejemplo de clase');
        $this->mail->AddReplyTo($input['from'], 'Ejemplo de clase');
        $this->mail->Subject = $input['subject'];
        $this->mail->AltBody = 'Message in plain text';
        $this->mail->MsgHTML($input['msg']);

        $this->mail->Send();

        $result->setCode(TRUE);
        $result->setMessage("Mensaje enviado a " . $input['to']);

    } catch (phpmailerException $e) {
        $result->setCode(FALSE);
        $result->setMessage("Error: " . $e->errorMessage());
    } catch (Exception $e) {
        $result->setCode(FALSE);
        $result->setMessage("Error: " . $e->getMessage());
    }
    return $result;

});


$app->post('/site', function ($request, $response) {
    $result = new Result();
//lock the table
    try {
        $input = $request->getParsedBody();
        $dbquery = $this->db->prepare("INSERT INTO " . TABLE . " (name, link, email) VALUES (?, ?, ?)");
        $dbquery->bindParam(1, $input['name']);
        $dbquery->bindParam(2, $input['link']);
        $dbquery->bindParam(3, $input['email']);
        $dbquery->execute();
        $number = $dbquery->rowCount();
        $lastId = $this->db->lastInsertId();
        if ($number > 0) {
            $result->setCode(TRUE);
            $result->setStatus(OK);
            $result->setLast($lastId);
        }
        else {
            $result->setCode(FALSE);
            $result->setStatus(NOT_COMPLETED);
            $result->setMessage("NOT INSERTED");
        }
    } catch (PDOException $e) {
        $result->setCode(FALSE);
        $result->setStatus(CONFLICT);
        $result->setMessage("Error: " . $e->getMessage());
    }
    return $this->response->withJson($result);
});

$app->put('/site/[{id}]', function ($request, $response, $args) {
    $result = new Result();
    try {
        $input = $request->getParsedBody();
        $dbquery = $this->db->prepare("UPDATE " . TABLE . " SET name = ?, link = ?, email = ? WHERE id = ?");
        $dbquery->bindParam(1, $input['name']);
        $dbquery->bindParam(2, $input['link']);
        $dbquery->bindParam(3, $input['email']);
        $dbquery->bindParam(4, $args['id']);
        $dbquery->execute();
        $number = $dbquery->rowCount();
        if ($number > 0) {
            $result->setCode(TRUE);
            $result->setStatus(OK);
        }
        else {
            $result->setCode(FALSE);
            $result->setStatus(NOT_COMPLETED);
            $result->setMessage("NOT UPDATED");
        }
    } catch (PDOException $e) {
        $result->setCode(FALSE);
        $result->setStatus(CONFLICT);
        $result->setMessage("Error: " . $e->getMessage());
    }
    return $this->response->withJson($result);
});

$app->delete('/site/[{id}]', function ($request, $response, $args) {
    $result = new Result();
    try {
        $dbquery = $this->db->prepare("DELETE FROM " . TABLE . " WHERE id = ?");
        $dbquery->bindParam(1, $args['id']);
        $dbquery->execute();
        $number = $dbquery->rowCount();
if ($number > 0) {
    $result->setCode(TRUE);
    $result->setStatus(OK);
}
else {
    $result->setCode(FALSE);
    $result->setStatus(NOT_COMPLETED);
    $result->setMessage("NOT DELETED");
}
} catch (PDOException $e) {
        $result->setCode(FALSE);
        $result->setStatus(CONFLICT);
        $result->setMessage("Error: " . $e->getMessage());
    }
    return $this->response->withJson($result);
});

?>
