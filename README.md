# API en Symfony que gestiona Clientes, Albaranes y Facturas.

## Pasos para Desplegar en Local.
1. Tener composer en el equipo.
2. En el .env adaptar el "DATABASE_URL" según la base de datos que se vaya a utilizar.
3. Ejecutar `composer install`.
4. php `bin/console doctrine:database:create`.
5. php `bin/console doctrine:migrations:migrate`.

_Sugerencia de Software para realizar las pruebas._
* Servidor: En la terminal escribir `symfony server:start` para desplegar con Symfony CLI.
* Peticiones: PostMan o similar.

## Rutas y parámetros de ejemplo para la realización de pruebas.

### "Home".
Las rutas `/` y `/api` llevan a un mensaje de bienvenida.

### Clientes.
#### Rutas:
* `/api/clientes` con **GET** devuelve todos los clientes.
* `/api/clientes/n` con **GET**, siendo 'n' un número entero, te muestra el cliente con ese 'id'.
* `/api/clientes` con **POST** crea un cliente.

#### JSON de ejemplo para crear un cliente.
```
{
    "idCliente": 1,
    "lineas": [
        {
            "producto": 1,
            "nombreProducto": "Producto 1",
            "cantidad": 1,
            "precioUnitario": 1.1
        }
    ]
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
```
{
    "idCliente": 1,
    "lineas": {
        "actualizar":[
            {
                "id":1,
                "producto": 2,
                "nombreProducto":"Producto 2",
                "cantidad": 2,
                "precioUnitario":2
            }
        ],
        "borrar":[1, 2],
        "crear":[
            {
                "producto":5,
                "nombreProducto":"Producto 5",
                "cantidad":5.5,
                "precioUnitario":5
            }
        ]
    }
}
```

### Facturas.
#### Rutas:
* `/api/facturas` con **POST** crea una nueva factura.

#### Ejemplo JSON crear una factura (POST).
```
{
    "albaranes": [1, 2]
}
```
