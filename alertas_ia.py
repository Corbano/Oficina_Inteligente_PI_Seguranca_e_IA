from flask import Flask, jsonify
from datetime import datetime, timedelta

# Aqui você importa sua IA existente
# from model.minha_ia import gerar_alertas_ia

app = Flask(__name__)

# Exemplo de clientes simulados
clientes = [
    {"nome": "João Silva", "veiculo": "Uno", "placa": "ABC1234", "ultima_troca": "2025-06-01"},
    {"nome": "Maria Oliveira", "veiculo": "Gol", "placa": "XYZ9876", "ultima_troca": "2025-03-15"},
    {"nome": "Pedro Santos", "veiculo": "Fiat 500", "placa": "LMN4567", "ultima_troca": "2025-05-10"}
]

@app.route("/alertas")
def alertas():
    alertas = []

    for c in clientes:
        # Aqui você chamaria sua IA real
        # resultado = gerar_alertas_ia(c)

        # Lógica simples de previsão (5 meses após a última troca)
        ultima = datetime.strptime(c["ultima_troca"], "%Y-%m-%d")
        prox_troca = ultima + timedelta(days=150)
        dias_restantes = (prox_troca - datetime.now()).days

        if dias_restantes <= 0:
            nivel = "Urgente"
        elif dias_restantes <= 30:
            nivel = "Próximo"
        else:
            nivel = "Futuro"

        mensagem = f"Olá {c['nome']}, seu {c['veiculo']} ({c['placa']}) precisa de manutenção. Nível: {nivel}."

        alertas.append({
            "cliente": c["nome"],
            "veiculo": c["veiculo"],
            "placa": c["placa"],
            "prox_troca": prox_troca.strftime("%Y-%m-%d"),
            "nivel": nivel,
            "mensagem": mensagem
        })

    return jsonify(alertas)

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5002)
