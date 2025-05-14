<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AlunniController
{
  public function index(Request $request, Response $response, $args){
    $queryParams = $request->getQueryParams();
    $search = "where nome regexp '$queryParams[search]' or cognome regexp '$queryParams[search]'" ?? "";
    $sortCol =$queryParams['sortCol'] ?? "id";
    $sort =$queryParams['sort'] ?? "ASC";
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $stmt = $mysqli_connection->prepare("SELECT * FROM alunni $search order by $sortCol $sort");
    $stmt->execute();
    $result = $stmt->get_result();
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }

  public function view(Request $request, Response $response, $args){
    $id = $args['id'];
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $stmt = $mysqli_connection->prepare("SELECT * FROM alunni WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($results = $result->fetch_all(MYSQLI_ASSOC)){
      $response->getBody()->write(json_encode($results));
      return $response->withHeader("Content-type", "application/json")->withStatus(200);
    }
    else{
      $response->getBody()->write('{"msg": "Not Found"}');
      return $response->withHeader("Content-type", "application/json")->withStatus(404);
    }
  }

  public function create(Request $request, Response $response, $args){
    $body = json_decode($request->getBody());
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $query = "INSERT INTO alunni (nome, cognome) VALUES (?, ?);";
    $stmt = $mysqli_connection->prepare($query);
    if($body->nome && $body->cognome){
      $stmt->bind_param("ss", $body->nome, $body->cognome);
      $stmt->execute();
      $response->getBody()->write('{"msg": "Created"}');
      return $response->withHeader("Content-type", "application/json")->withStatus(201);
    }
    else{
      $response->getBody()->write('{"msg": "Missing Params"}');
      return $response->withHeader("Content-type", "application/json")->withStatus(400);
    }
  }

  public function update(Request $request, Response $response, $args){
    $id = $args['id'];
    $body = json_decode($request->getBody());
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');

    $stmt = $mysqli_connection->prepare("SELECT * FROM alunni WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();

    if(!$results = $result->fetch_all(MYSQLI_ASSOC)){
      $response->getBody()->write('{"msg": "Not Found"}');
      return $response->withHeader("Content-type", "application/json")->withStatus(404);
    }

    $query = "UPDATE alunni SET nome=?, cognome=? WHERE id=?;";
    $stmt = $mysqli_connection->prepare($query);
    if($body->nome && $body->cognome){
      $stmt->bind_param("ssi", $body->nome, $body->cognome, $id);
      $stmt->execute();
      $response->getBody()->write('{"msg": "Updated"}');
      return $response->withHeader("Content-type", "application/json")->withStatus(200);
    }
    else{
      $response->getBody()->write('{"msg": "Missing Params"}');
      return $response->withHeader("Content-type", "application/json")->withStatus(400);
    }
  }

  public function destroy(Request $request, Response $response, $args){
    $id = $args['id'];
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $stmt = $mysqli_connection->prepare("SELECT * FROM alunni WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($results = $result->fetch_all(MYSQLI_ASSOC)){
      $stmt = $mysqli_connection->prepare("DELETE FROM alunni WHERE id=?");
      $stmt->bind_param("i",$id);
      $stmt->execute();

      $response->getBody()->write('{"msg": "Deleted"}');
      return $response->withHeader("Content-type", "application/json")->withStatus(200);
    }
    else{
      $response->getBody()->write('{"msg": "Not Found"}');
      return $response->withHeader("Content-type", "application/json")->withStatus(404);
    }
  }
}
