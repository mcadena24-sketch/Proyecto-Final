# Simicoin

Proyecto final para las asignaturas de **Criptografía** y **Teoría de la Información**  
Universidad Autónoma de Baja California Sur · Departamento de Sistemas Computacionales

---

## Descripción

Simicoin es un ecosistema de criptomoneda construido desde cero en Python. Implementamos una blockchain P2P con Prueba de Trabajo (PoW), firmas digitales ECDSA sobre la curva `secp256k1`, Árbol de Merkle, y una API REST que se conecta a una interfaz web desde la cual el usuario puede crear wallets, minar bloques y transferir tokens.

---

## Equipo

| Rol | Integrante |
|---|---|
| Desarrollador Principal | Martin Cadena |
| Ingeniero de Criptografía | Sergio Sosa |
| Ingeniero de Frontend | Samuel Sosa |
| Auditor de Calidad | Samuel Chio |

---

## Requisitos

- Python 3.8 o superior
- pip

### Instalación de dependencias

```bash
pip install flask flask-cors ecdsa requests
```

---

## Script de demo

Simula 3 nodos en red local, los conecta entre sí y corre una demostración completa de forma automática:

```bash
python demo_red.py
```

Para limpiar cadenas previas antes de iniciar:

```bash
python demo_red.py --limpio
```

El script realiza automáticamente:

1. Crea wallets A y B
2. Mina un bloque en el nodo 5000
3. Firma y registra una transacción A → B de 3 tokens
4. Mina un segundo bloque para confirmar la transacción
5. Sincroniza los nodos 5001 y 5002 con la cadena más larga
6. Muestra saldos finales y estado de validez de cada nodo
7. Deja establecida la conexión de manera local

---

## Interfaz Web

Con al menos el nodo 5000 activo, abre en el navegador:

```
http://localhost:5000
```

Ngrok 
Se conecta al pueto 5000, y nos arroga una url y con esa establecemos comunicacion con la web

Desde la web se puede:

- Crear el usuario y generar una wallet
- Entrar a la interfaz principal
- Minar bloques y recibir monedas
- Transferir monedas a otras direcciones
- Consultar el saldo y abrir el explorador de bloques

---

## API REST — Endpoints principales

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/new_wallet` | Genera un nuevo par de claves y dirección |
| GET | `/mine_block/<address>` | Mina un bloque y envía recompensa a la dirección |
| GET | `/get_chain` | Devuelve la cadena completa |
| GET | `/is_valid` | Valida la integridad de la cadena |
| GET | `/get_balance/<address>` | Consulta el saldo de una dirección |
| POST | `/sign_transaction` | Firma una transacción con clave privada |
| POST | `/add_transaction` | Registra una transacción firmada en el pool |
| POST | `/connect_node` | Conecta el nodo con otros nodos de la red |
| GET | `/replace_chain` | Aplica la regla de cadena más larga (consenso) |

---

## Arquitectura técnica

```
demo_red.py
│
├── blockchain.py (nodo 5000)  ←─┐
├── blockchain.py (nodo 5001)  ←─┤── sincronización P2P
└── blockchain.py (nodo 5002)  ←─┘
         │
         └── API REST (Flask) → Ngrok → Interfaz Web
```

### Componentes clave

- **Prueba de Trabajo:** dificultad fija de 4 ceros hexadecimales (`0000...`)
- **Merkle Root:** árbol binario de hashes SHA-256 sobre las transacciones del bloque
- **Wallets:** generadas localmente con `secp256k1`; la dirección son los últimos 40 caracteres del SHA-256 de la clave pública
- **Firmas:** ECDSA — cada transacción se firma con la clave privada del emisor y se verifica en el nodo antes de aceptarla
- **Persistencia:** cada nodo guarda su cadena en `blockchain_<puerto>.json`
- **Consenso:** regla de la cadena más larga — `/replace_chain` compara con todos los nodos registrados
