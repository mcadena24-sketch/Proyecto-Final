"""
blockchain.py  ·  Blockchain con PoW, Merkle Root, ECDSA (secp256k1) y Wallets
Dependencias:
    pip install flask flask-cors ecdsa
"""

import datetime
import hashlib
import json
import os
import sys
import requests
import socket
import threading

from ecdsa import SigningKey, VerifyingKey, SECP256k1, BadSignatureError
from flask import Flask, jsonify, request
from flask_cors import CORS


def generar_wallet():
    sk = SigningKey.generate(curve=SECP256k1, hashfunc=hashlib.sha256)
    vk = sk.get_verifying_key()

    return {
        'private_key': sk.to_string().hex(),
        'public_key': vk.to_string().hex(),
        'address': hashlib.sha256(vk.to_string()).hexdigest()[-40:]
    }


def firmar_transaccion(private_key_hex: str, tx: dict) -> str:
    mensaje = json.dumps(tx, sort_keys=True).encode()

    sk = SigningKey.from_string(
        bytes.fromhex(private_key_hex),
        curve=SECP256k1,
        hashfunc=hashlib.sha256
    )

    return sk.sign(mensaje).hex()


def verificar_firma(public_key_hex: str, tx: dict, firma_hex: str) -> bool:
    try:
        mensaje = json.dumps(tx, sort_keys=True).encode()

        vk = VerifyingKey.from_string(
            bytes.fromhex(public_key_hex),
            curve=SECP256k1,
            hashfunc=hashlib.sha256
        )

        return vk.verify(
            bytes.fromhex(firma_hex),
            mensaje
        )

    except (BadSignatureError, Exception):
        return False


class Blockchain:

    DIFICULTAD = 4
    RECOMPENSA_MINERO = 10

    def __init__(self, archivo_json='blockchain.json'):

        self.transacciones_pendientes = []
        self.wallets = {}
        self.nodes = set()
        self.archivo_json = archivo_json

        if os.path.exists(self.archivo_json):

            with open(self.archivo_json, 'r') as f:
                self.chain = json.load(f)

        else:

            self.chain = []
            self.create_block(
                proof=1,
                previous_hash='0'
            )

    def guardar_blockchain(self):

        with open(self.archivo_json, 'w') as f:
            json.dump(self.chain, f, indent=4)

    def calcular_merkle_root(self, transacciones):

        if not transacciones:
            return hashlib.sha256(b'').hexdigest()

        nivel = [
            hashlib.sha256(
                json.dumps(tx, sort_keys=True).encode()
            ).hexdigest()
            for tx in transacciones
        ]

        while len(nivel) > 1:

            if len(nivel) % 2 != 0:
                nivel.append(nivel[-1])

            nivel = [
                hashlib.sha256(
                    (nivel[i] + nivel[i + 1]).encode()
                ).hexdigest()

                for i in range(0, len(nivel), 2)
            ]

        return nivel[0]

    def create_block(self, proof, previous_hash, minero=None):

        if minero:

            self.transacciones_pendientes.insert(0, {
                'remitente': 'SISTEMA',
                'destinatario': minero,
                'monto': self.RECOMPENSA_MINERO,
                'public_key': None,
                'firma': None,
            })

        merkle_root = self.calcular_merkle_root(
            self.transacciones_pendientes
        )

        block = {
            'index': len(self.chain) + 1,
            'timestamp': datetime.datetime.utcnow().isoformat() + 'Z',
            'proof': proof,
            'nonce': proof,
            'previous_hash': previous_hash,
            'transacciones': self.transacciones_pendientes,
            'merkle_root': merkle_root,
        }

        block['block_hash'] = self.hash(block)

        self.transacciones_pendientes = []

        self.chain.append(block)

        self.guardar_blockchain()

        return block

    def get_previous_block(self):
        return self.chain[-1]

    def proof_of_work(self, previous_proof):

        objetivo = '0' * self.DIFICULTAD
        new_proof = 1

        while True:

            operacion = str(
                new_proof**2 - previous_proof**2
            ).encode()

            hash_result = hashlib.sha256(
                operacion
            ).hexdigest()

            if hash_result[:self.DIFICULTAD] == objetivo:
                return new_proof

            new_proof += 1

    def hash(self, block):

        # Excluir block_hash para no crear referencia circular
        bloque_limpio = {k: v for k, v in block.items() if k != 'block_hash'}

        encoded_block = json.dumps(
            bloque_limpio,
            sort_keys=True
        ).encode()

        return hashlib.sha256(encoded_block).hexdigest()

    def is_chain_valid(self, chain):

        objetivo = '0' * self.DIFICULTAD
        previous_block = chain[0]

        for block_index in range(1, len(chain)):

            block = chain[block_index]

            # Verificar que el hash del bloque anterior coincide
            bloque_sin_hash = {k: v for k, v in previous_block.items() if k != 'block_hash'}
            if block['previous_hash'] != self.hash(bloque_sin_hash):
                return False, "previous_hash inválido"

            # Verificar integridad del block_hash
            bloque_a_verificar = {k: v for k, v in block.items() if k != 'block_hash'}
            if block.get('block_hash') != self.hash(bloque_a_verificar):
                return False, f"block_hash inválido en bloque #{block['index']}"

            # Verificar PoW
            previous_proof = previous_block['proof']
            proof = block['proof']

            hash_op = hashlib.sha256(
                str(proof**2 - previous_proof**2).encode()
            ).hexdigest()

            if hash_op[:self.DIFICULTAD] != objetivo:
                return False, "PoW inválido"

            previous_block = block

        return True, "Cadena válida"


    def agregar_nodo(self, address):
        self.nodes.add(address)

    def reemplazar_cadena(self):

        network = self.nodes
        longest_chain = None
        max_length = len(self.chain)

        for node in network:

            try:

                response = requests.get(
                    f'http://{node}/get_chain'
                )

                if response.status_code == 200:

                    length = response.json()['longitud']
                    chain = response.json()['chain']

                    valida, _ = self.is_chain_valid(chain)

                    if length > max_length and valida:

                        max_length = length
                        longest_chain = chain

            except:
                pass

        if longest_chain:

            self.chain = longest_chain
            self.guardar_blockchain()

            return True

        return False

    def agregar_transaccion(
        self,
        remitente,
        destinatario,
        monto,
        public_key,
        firma
    ):

        tx_data = {
            'remitente': remitente,
            'destinatario': destinatario,
            'monto': monto,
        }

        if not verificar_firma(
            public_key,
            tx_data,
            firma
        ):
            return None, "Firma inválida"

        saldo = self.get_balance(remitente)

        if saldo < monto:
            return None, "Saldo insuficiente"

        self.transacciones_pendientes.append({
            **tx_data,
            'public_key': public_key,
            'firma': firma,
        })

        return self.get_previous_block()['index'] + 1, None

    def get_balance(self, address):

        saldo = 0

        for block in self.chain:

            for tx in block.get('transacciones', []):

                if tx['destinatario'] == address:
                    saldo += tx['monto']

                if tx['remitente'] == address:
                    saldo -= tx['monto']

        return saldo


port = 5000

if len(sys.argv) > 1:
    port = int(sys.argv[1])

app = Flask(__name__)
CORS(app)
blockchain = Blockchain(archivo_json=f'blockchain_{port}.json')


@app.route('/new_wallet', methods=['GET'])
def new_wallet():

    return jsonify(
        generar_wallet()
    ), 200


@app.route('/mine_block/<address>', methods=['GET'])
def mine_block(address):

    previous_block = blockchain.get_previous_block()

    proof = blockchain.proof_of_work(
        previous_block['proof']
    )

    previous_hash = blockchain.hash(
        previous_block
    )

    block = blockchain.create_block(
        proof,
        previous_hash,
        minero=address
    )

    for node in blockchain.nodes:

        try:
            requests.get(
                f'http://{node}/replace_chain'
            )
        except:
            pass

    return jsonify({
        'mensaje': '¡Bloque minado exitosamente!',
        'minero': address,
        'recompensa': blockchain.RECOMPENSA_MINERO,
        'index': block['index'],
        'timestamp': block['timestamp'],
        'proof': block['proof'],
        'previous_hash': block['previous_hash'],
        'merkle_root': block['merkle_root'],
        'transacciones': block['transacciones'],
    }), 200


@app.route('/get_chain', methods=['GET'])
def get_chain():

    return jsonify({
        'chain': blockchain.chain,
        'longitud': len(blockchain.chain),
    }), 200


@app.route('/is_valid', methods=['GET'])
def is_valid():

    valida, mensaje = blockchain.is_chain_valid(
        blockchain.chain
    )

    return jsonify({
        'valida': valida,
        'mensaje': mensaje
    }), 200


@app.route('/add_transaction', methods=['POST'])
def add_transaction():

    datos = request.get_json()

    indice, error = blockchain.agregar_transaccion(
        datos['remitente'],
        datos['destinatario'],
        datos['monto'],
        datos['public_key'],
        datos['firma'],
    )

    if error:
        return jsonify({'error': error}), 400

    return jsonify({
        'mensaje': f"Tx registrada en bloque #{indice}"
    }), 201


@app.route('/get_balance/<address>', methods=['GET'])
def get_balance(address):

    saldo = blockchain.get_balance(address)

    return jsonify({
        'address': address,
        'saldo': saldo
    }), 200




@app.route('/connect_node', methods=['POST'])
def connect_node():

    datos = request.get_json()

    nodes = datos.get('nodes')

    if nodes is None:
        return jsonify({
            'mensaje': 'No hay nodos'
        }), 400

    for node in nodes:
        blockchain.agregar_nodo(node)

    return jsonify({
        'mensaje': 'Nodos conectados',
        'total_nodes': list(blockchain.nodes)
    }), 201


@app.route('/replace_chain', methods=['GET'])
def replace_chain():

    replaced = blockchain.reemplazar_cadena()

    if replaced:

        return jsonify({
            'mensaje': 'Cadena reemplazada',
            'new_chain': blockchain.chain
        }), 200

    else:

        return jsonify({
            'mensaje': 'Cadena actual válida',
            'chain': blockchain.chain
        }), 200


@app.route('/sign_transaction', methods=['POST'])
def sign_transaction():

    datos = request.get_json()

    tx_data = {
        'remitente': datos['remitente'],
        'destinatario': datos['destinatario'],
        'monto': datos['monto'],
    }

    firma = firmar_transaccion(
        datos['private_key'],
        tx_data
    )

    sk = SigningKey.from_string(
        bytes.fromhex(datos['private_key']),
        curve=SECP256k1
    )

    public_key = sk.get_verifying_key().to_string().hex()

    return jsonify({
        'firma': firma,
        'public_key': public_key
    }), 200


if __name__ == '__main__':

    app.run(
        host='0.0.0.0',
        port=port,
        debug=True
    )
