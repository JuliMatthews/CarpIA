Webpay Plus

Webpay Plus permite realizar una solicitud de autorización financiera de un pago con tarjetas de crédito, débito Redcompra o prepago en donde quién realiza el pago ingresa al sitio del comercio, selecciona productos o servicio, e indica los datos asociados a la tarjeta del medio de pago seleccionado anteriormente, esta acción lo realiza en forma segura en Webpay. El comercio que recibe pagos mediante Webpay Plus es identificado mediante un código de comercio.

Es el tipo de transacción mas común, usada para un pago puntual en una tienda simple. Se generará un único cobro para todos los productos o servicios adquiridos por el tarjetahabiente.

Webpay Plus
Webpay Plus permite realizar una solicitud de autorización financiera de un pago con tarjetas de crédito o débito Redcompra en donde quién realiza el pago ingresa al sitio del comercio, selecciona productos o servicio, y el ingreso asociado a los datos de la tarjeta de crédito o débito Redcompra lo realiza en forma segura en Webpay Plus. El comercio que recibe pagos mediante Webpay Plus es identificado mediante un código de comercio.

Es el tipo de transacción mas común, usada para un pago puntual en una tienda simple. Se generará un único cobro para todos los productos o servicios adquiridos por el tarjetahabiente.

Flujo en caso de éxito
De cara al tarjetahabiente, el flujo de páginas para la transacción es el siguiente:

sitio del comercio -> webpay formulario de pago -> autenticacion en banco emisor -> sitio del comercio

Desde el punto de vista técnico, la secuencia es la siguiente:

Flujo en caso de éxito

Participantes:
- Tarjetahabiente
- Comercio
- Webpay

1. Tarjetahabiente -> Comercio
   Pagar con Webpay

2. Comercio -> Webpay
   Crear una transacción

3. Webpay -> Comercio
   Retorna Token y URL formulario de pago

4. Comercio -> Tarjetahabiente
   Redirecciona hacia formulario de pago

5. Tarjetahabiente -> Webpay
   Petición HTTPS hacia URL del formulario enviando el Token

6. Webpay -> Tarjetahabiente
   Formulario de pago Webpay

7. Tarjetahabiente -> Webpay
   Paga

8. Webpay
   Autoriza

9. Webpay -> Tarjetahabiente
   Redirecciona hacia el sitio del comercio

10. Tarjetahabiente -> Comercio
    Petición hacia sitio del comercio

11. Comercio -> Webpay
    Confirmar transacción usando Token

12. Webpay -> Comercio
    Retorna resultado de la transacción

13. Webpay
    Puede volver a consultar el estado utilizando el mismo Token

14. Comercio -> Tarjetahabiente
    Despliega voucher


    Una vez seleccionado los bienes o servicios, el tarjetahabiente decide pagar a través de Webpay.
El comercio inicia una transacción en Webpay.
Webpay procesa el requerimiento y entrega como resultado de la operación el token de la transacción y URL de redireccionamiento a la cual se deberá redirigir al tarjetahabiente.
Comercio redirecciona al tarjetahabiente hacia Webpay, con el token de la transacción a la URL indicada en punto 3. La redirección se realiza enviando por método POST el token en variable token_ws.
El navegador Web del tarjetahabiente realiza una petición HTTPS a Webpay, en base al redireccionamiento generado por el comercio en el punto 4.
Webpay responde al requerimiento desplegando el formulario de pago de Webpay. Desde este punto la comunicación es entre Webpay y el tarjetahabiente, sin interferir el comercio. El formulario de pago de Webpay despliega, entre otras cosas, el monto de la transacción, información del comercio como nombre y logotipo, las opciones de pago a través de crédito o débito.
Tarjetahabiente ingresa los datos de la tarjeta, hace clic en pagar en formulario Webpay. El tiempo en el cual permanece el formulario de Webpay depende del ambiente, en producción el tiempo es de 4 minutos y en integración es de 10 minutos. En caso extender dicho plazo y no haber terminado la transacción, esta será abortada automáticamente.

 En caso de que se cumpla el tiempo máximo para completar el formulario, el comercio recibirá las variables TBK_ID_SESSION y TBK_ORDEN_COMPRA.
Webpay procesa la solicitud de autorización (primero autenticación bancaria y luego la autorización de la transacción).
Una vez resuelta la autorización, Webpay retorna el control al comercio, realizando un redireccionamiento HTTPS hacia la página de transición del comercio, enviando el token de la transacción en la variable token_ws. En la versión 1.1 y superiores de la API, esta redirección es por GET. Para versiones anteriores se envía por método POST. El comercio debe implementar la recepción de esta variable.
El navegador Web del tarjetahabiente realiza una petición HTTPS al sitio del comercio, en base a la redirección generada por Webpay en el punto 9.
El sitio del comercio recibe la variable token_ws e invoca el segundo método Web para confirmar y obtener el resultado de la autorización. El resultado de la autorización podrá ser consultado posteriormente con la variable anteriormente mencionada.
Comercio recibe el resultado de la confirmación.
 En la versión anterior de Webpay, había que invocar acknowledgeTransaction() para informar a WebPay que se había recibido el resultado la transacción sin problemas. Ahora no es necesario, ya que esto se realiza de forma automática una vez que se confirma la transacción. Además ya no se debe mostrar el voucher de Transbank, solo debe mostrarse desde el sitio del comercio.
Sitio del comercio despliega voucher con los datos de la transacción.

Flujo si usuario aborta el pago
Si el tarjetahabiente anula la transacción en el formulario de pago de Webpay, el flujo cambia y los pasos son los siguientes:

Flujo si usuario aborta el pago

Participantes:
- Tarjetahabiente
- Comercio
- Webpay

1. Tarjetahabiente -> Comercio
   Pagar con Webpay

2. Comercio -> Webpay
   Crear una transacción

3. Webpay -> Comercio
   Retorna Token y URL formulario de pago

4. Comercio -> Tarjetahabiente
   Redirecciona hacia formulario de pago

5. Tarjetahabiente -> Webpay
   Petición HTTPS hacia URL del formulario enviando el Token

6. Webpay -> Tarjetahabiente
   Formulario de pago Webpay

7. Tarjetahabiente -> Webpay
   Anula el pago

8. Webpay -> Tarjetahabiente
   Redirecciona hacia el sitio del comercio

9. Tarjetahabiente -> Comercio
   Petición HTTPS hacia URL de retorno enviando Token

10. Comercio -> Webpay
    Recibe Token y se consulta método para validar status

11. Webpay -> Comercio
    Retorna status de transacción abortada

12. Comercio -> Tarjetahabiente
    Avisa al tarjetahabiente que no se completó el pago


Una vez seleccionado los bienes o servicios, tarjetahabiente decide pagar a través de Webpay.
El comercio inicia una transacción en Webpay.
Webpay procesa el requerimiento y entrega como resultado de la operación el token de la transacción y URL de redireccionamiento a la cual se deberá redirigir al tarjetahabiente.
Comercio redirecciona al tarjetahabiente hacia Webpay, con el token de la transacción a la URL indicada en punto 3. La redirección se realiza enviando por método POST el token en variable token_ws.
El navegador Web del tarjetahabiente realiza una petición HTTPS a Webpay, en base al redireccionamiento generado por el comercio en el punto 4.
Webpay responde al requerimiento desplegando el formulario de pago de Webpay. Desde este punto la comunicación es entre Webpay y el tarjetahabiente, sin interferir el comercio. El formulario de pago de Webpay despliega, entre otras cosas, el monto de la transacción, información del comercio como nombre y logotipo, las opciones de pago a través de crédito o débito.
Tarjetahabiente hace clic en “anular”, en formulario Webpay.
Webpay retorna el control al comercio, realizando un redireccionamiento HTTPS hacia la página de retorno del comercio, en donde se envía por método GET el token de la transacción en la variable TBK_TOKEN además de las variables TBK_ORDEN_COMPRA y TBK_ID_SESION (para el entorno de integración, este redireccionamiento es realizado con el método POST).

 Nota que el nombre de las variables recibidas es diferente. En lugar de token_ws acá el token viene en la variable TBK_TOKEN.
El comercio con la variable TBK_TOKEN consulta la transacción para validar el estado (no es necesario
confirmar la transacción).

El comercio debe informar al tarjetahabiente que su pago no se completó.

Resumen de flujos
A la URL de return_url siempre se llega por POST, aunque desde la versión 1.1 del API, en adelante, la redirección es por GET (solo en el caso de pago abortado en el ambiente de integración, el retorno se mantiene por POST). Para resumir los diferentes flujos que pueden existir, y las diferentes respuestas que se pueden esperar: Hay 4 diferentes flujos, donde cada uno llega con datos distintos:

Flujo normal: El usuario al finalizar la transacción (tanto si es un rechazo o una aprobación) llegará solamente token_ws.
Timeout (Tiempo excedido en el formulario de Webpay): El tiempo es de 4 minutos para el ambiente de producción y 10 minutos para el entorno de integración.Llegará solamente TBK_ID_SESION que contiene el session_id enviado al crear la transacción, TBK_ORDEN_COMPRAque representa el buy_order enviado. No llegará token.
Pago abortado (con botón anular compra en el formulario de Webpay): Llegará TBK_TOKEN (notar que no se llama token_ws, pero igualmente contiene el token de la transacción), TBK_ID_SESION, TBK_ORDEN_COMPRA
*Si ocurre un error en el formulario de pago, y hace click en el link de "volver al sitio" de la pantalla de error: (replicable solo en producción si inicias una transacción, abres el formulario de pago, cierras el tab de Chrome y luego lo recuperas) Llegará token_ws, TBK_TOKEN, TBK_ID_SESION, TBK_ORDEN_COMPRA.


REFERENCIA API

Ambientes y Credenciales
La API REST de Webpay está protegida para garantizar que solamente comercios autorizados por Transbank hagan uso de las operaciones disponibles. La seguridad esta implementada mediante los siguientes mecanismos:

Canal seguro a través de TLSv1.2 para la comunicación del cliente con Webpay.
Autenticación y autorización mediante el intercambio de headers Tbk-Api-Key-Id (código de comercio) y Tbk-Api-Key-Secret (llave secreta).

Ambiente de Producción
Las URLs de endpoints de producción están alojados dentro de https://webpay3g.transbank.cl/.
Host: https://webpay3g.transbank.cl

Ambiente de Integración
Las URLs de endpoints de integración están alojados dentro de https://webpay3gint.transbank.cl/.
Host: https://webpay3gint.transbank.cl

Credenciales del Comercio
Todas las peticiones que hagas deben incluir el código de comercio y la llave secreta entregada por Transbank, actuando ambas como las credenciales que autorizan distintas operaciones.
Tbk-Api-Key-Id: Código de comercio
Tbk-Api-Key-Secret: Llave secreta
Content-Type: application/json

Ten en cuenta que tu(s) código(s) de comercio en ambiente de producción no son iguales a los entregados para el ambiente de integración.

Códigos de comercio
En la documentación puedes revisar todos los códigos de comercio del ambiente de integración:
Cómo empezar
Para empezar a integrar los productos de Transbank, te recomendamos usar nuestros SDK y plugins, disponibles para múltiples lenguajes de programación y plataformas. En general, existe un único Transbank SDK para el backend de tu e-commerce, el cual te permite operar con todos nuestros productos.

Si quieres implementar Webpay Plus, te recomendamos revisar nuestros plugins oficiales.

En esta sección veremos los pasos para comenzar con el SDK que corresponda al lenguaje de programación que utilices en tu backend.

Flujo de Integración
Inicialmente, el comercio tendrá algunas tareas comerciales que realizar mientras ocurre el proceso de integración. Este proceso de afiliación comercial se puede realizar en paralelo al proceso técnico de integración.
A continuación, puedes conocer el flujo completo.

Hazte Cliente

Completa el formulario de afiliación en nuestro portal publico.transbank.cl, fírmalo digitalmente y recibirás tu código de comercio en el correo que registraste.

Integrate

Realiza la integración del medio de pago en tu página web o aplicación. Consulta las herramientas disponibles en Transbank Developers y Slack.

Valida

Para validar tu integración y verificar que funcione correctamente, ingresa a Transbank Developers, y completa el formulario de validación. Puedes realizar consultas en soporte@transbank.cl

Vende

Finalizado el proceso de validación se te asignará una llave privada (Tbk-Api-Key-Secret). Configúrala en tu app o sitio web y ya estarás ¡Listo para vender!


Proceso técnico de integración
Este proceso contempla todas las tareas necesarias que debe realizar el comercio para integrar el producto contratado dentro de sus sistemas.

A) Usando un plugin
Si quieres implementar Webpay Plus con alguno de nuestros plugins oficiales, revisa su documentación específica. En ese caso, el proceso es más simple y no requiere escribir código como en el caso de los SDK, ya que basta con realizar la instalación y configuración del plugin en la plataforma que estés utilizando.

B) Usando un SDK
Para instalar el SDK, debes agregarlo al gestor de dependencias de tu lenguaje:

En Java debes agregar esta entrada en tu archivo pom.xml de Maven:

<dependency>
    <groupId>com.github.transbankdevelopers</groupId>
    <artifactId>transbank-sdk-java</artifactId>
    <version>{mira-en-github-la-ultima-version-disponible}</version>
</dependency>


Te recomendamos leer las instrucciones de instalación detalladas para el SDK Java para más opciones e información de la última versión disponible.

En PHP puedes usar composer (si no lo tienes, puedes instalarlo desde acá) para descargar la última versión del SDK, ejecutando esto en la línea de comandos cuando estés en la raíz de tu proyecto:

composer require transbank/transbank-sdk:^5.0
Te recomendamos leer las instrucciones de instalación detalladas para el SDK PHP para más opciones de instalación.
https://github.com/TransbankDevelopers/transbank-sdk-php#instalaci%C3%B3n

En .NET puedes instalar el SDK desde la línea de comandos del Package Manager de Visual Studio:

PM> Install-Package TransbankSDK
Te recomendamos leer las instrucciones de instalación detalladas para el SDK .NET para más opciones de instalación.

En Ruby puedes instalar el SDK como una gema:

gem install transbank-sdk
Te recomendamos leer las instrucciones de instalación detalladas para el SDK Ruby para más opciones de instalación.

En Python puedes instalar el SDK desde PyPI:

pip install transbank-sdk
En NodeJS puedes instalar el SDK desde NPM:

npm install transbank-sdk 
Te recomendamos leer las instrucciones de instalación detalladas para el SDK Python para más opciones de instalación.
https://github.com/TransbankDevelopers/transbank-sdk-python#instalaci%C3%B3n

C) Usando el API REST
También puedes consumir el API REST de los productos directamente. Si usas un lenguaje de programación que no tiene un SDK oficial o simplemente quieres conectarte directamente al API, debes revisar la Referencia del API REST en el tab "http" para conocer los diferentes endpoints de cada producto, sus parámetros de entrada y parámetros de respuesta.

Ejemplos
Aquí encontrarás una visión completa y actualizada del estado de los diversos proyectos de ejemplo que integran los productos Webpay y POS, a través de los distintos SDK’s que tenemos disponibles.
Proyectos de Ejemplo del SDK para PHP
Este proyecto te brinda la oportunidad experimentar con las diversas modalidades de productos que Transbank ofrece a través de su SDK compatible con PHP. Conoce de manera práctica las soluciones y servicios que Transbank pone a tu disposición, permitiéndote comprender cómo integrar estas herramientas tecnológicas en tus proyectos y aplicaciones. ¡Explora las opciones disponibles y descubre cómo aprovechar al máximo estas capacidades!

El producto más usado para realizar un pago online. Se genera un único cobro para todos los productos o servicios adquiridos por el tarjetahabiente (carro de compras).

Webpay Plus - Creación de transacción
En esta etapa, se procederá a la creación de una transacción con el fin de obtener un identificador único. Esto nos permitirá redirigir al Tarjetahabiente hacia el formulario de pago en el siguiente paso.

Paso 1: Petición
Comienza por importar la librería WebpayPlus en tu proyecto.
Luego, crea una transacción utilizando las funciones proporcionadas mediante el SDK.
use Transbank\Webpay\WebpayPlus\Transaction;
use Transbank\Webpay\Options;
//configuración de la transacción
$option = new Options(API_KEY, COMMERCE_CODE, Options::ENVIRONMENT_INTEGRATION);
$transaction = new Transaction($option);
$response = $transaction->create($buyOrder, $sessionId, $amount, $returnUrl);

Paso 2: Respuesta
Una vez que hayas creado la transacción, aquí encontrarás los datos de respuesta generados por el proceso.

copy

{
    "token": "01ab3beeb590e7d1d27608ebd381d79573c1aab2e9312819fd1110c745ea2b8a",
    "url": "https://webpay3gint.transbank.cl/webpayserver/initTransaction"
}
    
Paso 3: Creación del formulario
Utiliza estos datos de respuesta para redireccionar al usuario al formulario de pago al Tarjetahabiente. Este formulario será la interfaz a través de la cual el usuario realizará su transacción.

copy

<form action="https://webpay3gint.transbank.cl/webpayserver/initTransaction" method="POST">
    <input type="hidden" name="token_ws" value="01ab3beeb590e7d1d27608ebd381d79573c1aab2e9312819fd1110c745ea2b8a" />
    <input type="submit" value="Pagar" />
<form>
    
Ejemplo
Para llevar a cabo una transacción de compra en nuestro sistema, primero debemos crear la transacción. Utilizaremos los siguientes datos para configurar la transacción:

Campo
Valor
buyOrder
O-7547
sessionId
S-4279
returnUrl
https://proyecto-ejemplo-php.transbankdevelopers.cl/webpay-plus/commit
amount
1692
Por último, con la respuesta del servicio que confirma la creación de la transacción, procedemos a crear el formulario de pago. Para fines de este ejemplo, haremos visible el campo "token_ws", el cual es esencial para completar el proceso de pago de manera exitosa.

Antes de continuar al formulario de Webpay, asegúrate de contar con los datos de las tarjetas de prueba que están en la documentación.
Formulario de redirección
Token
01ab3beeb590e7d1d27608ebd381d79573c1aab2e9312819fd1110c745ea2b8a
info logo
El token generado en esta aplicación se renueva automáticamente cada 5 minutos.

WebPay OneClick
Permite realizar pagos con un solo clic en un comercio habitual para el tarjetahabiente, una vez que este haya registrado su tarjeta en el comercio.

Oneclick Mall - Creación de transacción
En esta etapa comienza el proceso de inscripción del medio de pago. Este paso inicial es fundamental, para dirigir al tarjetahabiente al formulario de inscripción.

Todas las transacciones en este proyecto de ejemplo son realizadas en ambiente de integración.

Paso 1: Petición
Comienza por importar la librería Oneclick en tu proyecto.
Después podrás iniciar una inscripción.
copy

use Transbank\Webpay\Options;
use Transbank\Webpay\Oneclick\MallInscription;
use Transbank\Webpay\Oneclick\MallTransaction;
//configuración de la transacción
$option = new Options(self::API_KEY, self::COMMERCE_CODE, Options::ENVIRONMENT_INTEGRATION);
$mallInscription = new MallInscription($option);
$resp = $mallInscription->start($startTx["userName"], $startTx["email"], $startTx["responseUrl"]);
    
Paso 2: Respuesta
Una vez que hayas iniciado la inscripción, aquí encontrarás los datos de respuesta generados por el proceso.

copy

{
    "token": "01ab3ea0050639ee8ca0a6d7ea176c3bf8babf4c6b6900e074b51accf8beaa44",
    "urlWebpay": "https://webpay3gint.transbank.cl/webpayserver/bp_multicode_inscription.cgi"
}
    
Paso 3: Creación del formulario
Utiliza estos datos de respuesta para generar y presentar un formulario de Inscripción al Tarjetahabiente.

copy

<form action="https://webpay3gint.transbank.cl/webpayserver/initTransaction" method="POST">
    <input type="hidden" name="TBK_TOKEN" value="01ab3ea0050639ee8ca0a6d7ea176c3bf8babf4c6b6900e074b51accf8beaa44" />
    <input type="submit" value="Inscribir" />
<form>
    
Ejemplo
Para llevar a cabo una Inscripción en nuestro sistema, primero debemos crearla. Utilizaremos los siguientes datos para configurar la inscripción:

Campo
Valor
username
User-6991
email
user.6762@example.cl
response_url
https://proyecto-ejemplo-php.transbankdevelopers.cl/oneclick-mall/finish
Por último, con la respuesta del servicio que confirma la creación de la transacción, procedemos a crear el formulario de pago. Para fines de este ejemplo, haremos visible el campo "TBK_TOKEN", el cual es esencial para completar el proceso de pago de manera exitosa.

Antes de continuar al formulario de Webpay, asegúrate de contar con los datos de las tarjetas de prueba que están en la documentación.
Formulario de redirección
Token
01ab3ea0050639ee8ca0a6d7ea176c3bf8babf4c6b6900e074b51accf8beaa44
info logo
El token generado en esta aplicación se renueva automáticamente cada 5 minutos.

