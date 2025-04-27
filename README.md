# API en Symfony que gestiona Clientes, Albaranes y Facturas.

## Pasos para Desplegar en Local.
1. Tener composer en el equipo y al menos PHP 8.2. con estas extensiones: Ctype, iconv, PCRE, Session, SimpleXML, and Tokenizer.
2. En el .env adaptar el "DATABASE_URL" según la base de datos que se vaya a utilizar. El "APP_ENV" está puesto en producción, cambiar si se desea.
3. Ejecutar `composer install`.
4. php `bin/console doctrine:database:create`.
5. php `bin/console doctrine:migrations:migrate`.

_Sugerencia de Software para realizar las pruebas._
* Servidor: En la terminal escribir `symfony server:start` para desplegar con Symfony CLI.
* Peticiones: PostMan o similar.

## Rutas y parámetros de ejemplo para la realización de pruebas.
En todos los casos donde la petición contiene un JSON se comprueba el tipo de los parámetros y obliga a que sea el correcto.

### "Home".
Las rutas `/` y `/api` llevan a un mensaje de bienvenida.

### Clientes.
#### Rutas:
* `/api/clientes` con **GET** devuelve todos los clientes.
* `/api/clientes/n` con **GET**, siendo 'n' un número entero, te muestra el cliente con ese 'id'.
* `/api/clientes` con **POST** crea un cliente.

#### JSON de ejemplo para crear un cliente.
Ambos campos son obligatorios.

```
{
    "nombre": "Cliente de Prueba",
    "direccion": "Dirección de prueba"
}
```

### Albaranes.
#### Rutas:
* `/api/albaranes` con **GET** devuelve todos los albaranes.
* `/api/albaranes/n` con **GET** muestra el albarán con ese 'id'.
* `/api/albaranes` con **POST** crea un nuevo albarán.
* `/api/albaranes/n` con **PATH** modifica el albarán con ese 'id'.
* `/api/albaranes/n` con **DELETE** elimina el albarán con ese 'id'.

#### Ejemplos JSON.
##### Crear un albarán (POST).
* Es obligatorio que estén presentes el **'idCliente** y **'lineas'**, no pueden ser null pero se admite `"lineas:[]`. 
* El cliente debe existir.

```
{
    "idCliente": 1,
    "lineas": 
    [
        {
            "producto": 4,
            "nombreProducto": "Producto 4",
            "cantidad": 4.2,
            "precioUnitario": 4.2
        },
        {
            "producto": 5,
            "nombreProducto": "Producto 5",
            "cantidad": 4.2,
            "precioUnitario": 4.2
        },
        {
            "producto": 6,
            "nombreProducto": "Producto 6",
            "cantidad": 4.2,
            "precioUnitario": 4.2
        }
    ]
}
```

##### Modifica un albarán (PATH).
* Los parámetros **'idCliente'**, **'lineas'**, **'actualizar'**, **'borrar'**, **'crear'** son opcionales: si no se incluyen no se aplican sus respectivos cambios.
* También es válido que los arrays estén vacíos como por ejemplo  `"borrar": []`, simplemente se ignoran. Pero no pueden ser null.
* Si el albarán ya está facturado no permite modificación y lanza un error. 
* Si el 'id' del albarán pasado en la ruta no existiera no hace nada.
* Si en **'borrar'** no encuentra la línea la ignora.
* Si el ID de algunas de las líneas a actualizar no existiera lanza un error.

```
{
    "idCliente": 1,
    "lineas": {
        "actualizar":[
            {
                "id":1,
                "producto": 1,
                "nombreProducto":"Producto actualizado a 1.1",
                "cantidad": 1,
                "precioUnitario": 1
            },
            {
                "id":2,
                "producto": 2,
                "nombreProducto":"Producto actualizado y a punto de desaparecer 2",
                "cantidad": 2,
                "precioUnitario":2
            }
        ],
        "borrar":[2],
        "crear":[
            {
                "producto":5,
                "nombreProducto":"Producto 5",
                "cantidad":5.5,
                "precioUnitario":5
            },
            {
                "producto": 2,
                "nombreProducto":"Producto 2 nuevo",
                "cantidad": 2,
                "precioUnitario": 2
            }
        ]
    }
}
```

### Facturas.
#### Rutas:
* `/api/facturas` con **POST** crea una nueva factura.

#### Ejemplo JSON crear una factura a partir de uno o varios albaranes (POST).
* No permite facturar un albarán que ya estuviera facturado.
* No permite *null* ni un array vacío.
* Si el albarán no existe lanza un error.
* Todos lo albaranes deben ser del mismo cliente o lanza un error.

```
{
    "albaranes": [1, 3]
}
```
