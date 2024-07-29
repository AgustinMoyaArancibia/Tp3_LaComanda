<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/usuarioController.php';
require_once './db/accesoDatos.php';
require_once './controllers/productoController.php';
require_once './controllers/mesaController.php';
require_once './controllers/pedidoController.php';
require_once './middlewares/logger.php';
require_once './middlewares/autentificadorJWT.php';
require_once './controllers/empleadoController.php';
require_once './controllers/encuestaController.php';
/* 

 */

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();
$app->setBasePath('/api');

$app->addRoutingMiddleware(); 

$app->addBodyParsingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

date_default_timezone_set("America/Argentina/Buenos_Aires");

$app->get('[/]', function (Request $request, Response $response, $args) {
    $response->getBody()->write("TP La Comanda");
    return $response;
});

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/traeruno/{id}', \UsuarioController::class . ':TraerUno');
  $group->put('/{id}', \UsuarioController::class . ':ModificarUno');
  $group->delete('/{id}', \UsuarioController::class . ':BorrarUno');
  $group->get('/listarpdf', \UsuarioController::class . ':GenerarPDF');
})->add(\Logger::class . ':VerificadorAdmin');

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/traeruno/{id}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
  $group->put('[/]', \ProductoController::class . ':ModificarUno');
  $group->delete('/{id}', \ProductoController::class . ':BorrarUno');
  $group->get('/listarpdf', \ProductoController::class . ':GenerarPDF');
})->add(\Logger::class . ':VerificadorUsuariosRegistrados');

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos')->add(\Logger::class . ':VerificadorAdminOMozo');
  $group->get('/traeruno/{idMesa}', \MesaController::class . ':TraerUno')->add(\Logger::class . ':VerificadorAdminOMozo');
  $group->post('[/]', \MesaController::class . ':CargarUno')->add(\Logger::class . ':VerificadorAdmin');
  $group->put('[/]', \MesaController::class . ':ModificarUno')->add(\Logger::class . ':VerificadorAdminOMozo');
  $group->put('/cerrar', \MesaController::class . ':CerrarMesa')->add(\Logger::class . ':VerificadorAdmin');
  $group->delete('/{idMesa}', \MesaController::class . ':BorrarUno')->add(\Logger::class . ':VerificadorAdmin');
  $group->get('/listarpdf', \MesaController::class . ':GenerarPDF')->add(\Logger::class . ':VerificadorAdminOMozo');
});


$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->get('/estado', \PedidoController::class . ':TraerTodosPorEstado');
  $group->get('/traeruno/{id}', \PedidoController::class . ':TraerUno');
  $group->get('/mesamasusada', \PedidoController::class . ':MasUsada');
  $group->post('[/]', \PedidoController::class . ':CargarUno');
  $group->put('/modificaruno/{id}', \PedidoController::class . ':ModificarUno');
  $group->delete('/{id}', \PedidoController::class . ':BorrarUno');
   $group->put('/entregado', \EmpleadoController::class . ':EntregarPedido');
  $group->put('/cobrado', \EmpleadoController::class . ':CobrarCuenta'); 
  $group->get('/listarpdf', \PedidoController::class . ':GenerarPDF');
  $group->get('/pendientes', \PedidoController::class . ':TraerPendientesPerfil');
  $group->get('/enPreparacion', \PedidoController::class . ':TraerEnPreparacionPerfil');
  $group->get('/listos', \PedidoController::class . ':TraerlistosPerfil');
  $group->get('/generarPDFPedidoMesaCliente', \PedidoController::class . ':GenerarPDFPedidoMesaCliente');

  
  
})->add(\Logger::class . ':VerificadorAdminOMozo');

$app->group('/empleados', function (RouteCollectorProxy $group) {
  $group->get('/{perfil}', \EmpleadoController::class . ':ListarPedidos');
  $group->put('/enpreparacion', \EmpleadoController::class . ':PedidoEnPreparacion');
  $group->put('/pedidolisto', \EmpleadoController::class . ':PedidoListo');
})->add(\Logger::class . ':VerificadorAdminOMozo');


$app->group('/socios', function (RouteCollectorProxy $group) {
  $group->get('/pedidos/mesa/{idMesa}', \PedidoController::class . ':TraerTodosPorMesa');
  $group->post('/carga', \ProductoController::class . ':CargarCSV');
  $group->post('/descarga', \ProductoController::class . ':DescargarCSV');
  $group->get('/encuestas', \EncuestaController::class . ':TraerTodos');
  $group->get('/encuestas/mejorescomentarios', \EncuestaController::class . ':ListarMejoresComentarios'); 
  $group->get('/pedidosTardes', \PedidoController::class . ':TraerPedidosNoEntregadosATiempo');
  $group->get('/descargarLogoEmpresaPDF', \PedidoController::class . ':DescargarLogoEmpresaPDF');
  $group->get('/cantidadXsector', \PedidoController::class . ':ObtenerCantidadOperacionesPorSector');
  $group->get('/productosOrdenadosPorVentas', \ProductoController::class . ':ObtenerProductosOrdenadosPorVentas');
  $group->get('/obtenerDiasYHorariosPorEmpleado', \UsuarioController::class . ':ObtenerDiasYHorariosPorEmpleado');
  $group->get('/mesasOrdenadasPorFactura', \MesaController::class . ':ObtenerMesasOrdenadasPorFactura');
  $group->get('/facturacionMesaEntreFechas/{idMesa}', \MesaController::class . ':ObtenerFacturacionMesaEntreFechas');
  $group->get('/pedidosxmesa/{idMesa}', \PedidoController::class . ':TraerTodosPorMesa');
})->add(\Logger::class . ':VerificadorAdmin');

$app->group('/login', function (RouteCollectorProxy $group) {
  $group->post('[/]', \UsuarioController::class . ':ValidarUsuario');
});

$app->group('/clientes', function (RouteCollectorProxy $group) {
  $group->get('/{id}', \PedidoController::class . ':VerificarHoraEntrega');
  $group->post('/carga', \EncuestaController::class . ':CargarUno');
});

// Run app
$app->run();

