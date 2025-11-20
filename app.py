from flask import Flask, request, jsonify
from flask_cors import CORS
import datetime # Usaremos para pegar o ano atual

app = Flask(__name__)
CORS(app) 

# ✅ Rota de verificação (teste rápido)
@app.route('/status', methods=['GET'])
def status():
    return jsonify({"status": "Servidor Flask está rodando com sucesso!"})

# ✅ Rota de integração com PHP (AGORA MAIS INTELIGENTE)
@app.route('/prever', methods=['POST'])
def prever():
    try:
        dados = request.get_json()
        
        # 1. Coletar os novos dados
        carro = dados.get('carro')
        ano = dados.get('ano')
        km = dados.get('km')
        frequencia_uso = dados.get('frequencia_uso') # Novo campo

        # Validação de entrada
        if not all([carro, ano, km, frequencia_uso]):
            return jsonify({"erro": "Dados incompletos (carro, ano, km, frequencia_uso)"}), 400

        # --- INÍCIO DA NOVA LÓGICA DE "IA" ---
        
        # Inicializa as variáveis
        diagnostico = "✅ Veículo em boas condições." # Mensagem padrão
        checklist = [] # Lista de itens para checar
        
        km = int(km)
        ano = int(ano)
        ano_atual = datetime.datetime.now().year
        
        idade_veiculo = ano_atual - ano
        if idade_veiculo <= 0:
            idade_veiculo = 1 # Evita divisão por zero

        media_km_ano = km / idade_veiculo
        
        # --- Conjunto de Regras ---

        # REGRA 1: Quilometragem muito alta (desgaste geral)
        if km > 120000:
            diagnostico = "⚠️ Risco Moderado: Alta quilometragem sugere foco em desgaste."
            checklist.extend([
                "Verificar correia dentada (se aplicável, risco de rompimento)",
                "Checar sistema de arrefecimento (mangueiras, radiador)",
                "Inspecionar velas e cabos de vela",
                "Verificar suspensão (amortecedores e pivôs)"
            ])

        # REGRA 2: Uso severo (alta KM para o ano)
        if media_km_ano > 25000 and frequencia_uso == 'diario':
            diagnostico = "⚠️ Risco Alto: Veículo com indícios de uso severo."
            checklist.extend([
                "Verificar sistema de freios (pastilhas e discos)",
                "Troca de filtro de ar e filtro de combustível",
                "Inspecionar sistema de transmissão (óleo do câmbio)"
            ])

        # REGRA 3: Uso raro (carro muito parado)
        if frequencia_uso == 'raro' and idade_veiculo > 5:
            diagnostico = "✅ Atenção: Uso raro. Focar em itens que ressecam ou vencem por tempo."
            checklist.extend([
                "Inspecionar mangueiras de combustível e arrefecimento (risco de ressecamento)",
                "Verificar estado e validade dos pneus",
                "Checar bateria e sistema de partida",
                "Verificar validade do óleo do motor (trocar por tempo, não por KM)",
                "Verificar filtro de gasolina (pode entupir com gasolina velha)"
            ])
            
        # REGRA 4: Revisão de rotina (a cada 10.000 km)
        # Se nenhuma outra regra foi ativada E está perto da revisão
        if not checklist and (km % 10000 < 1000 or km % 10000 > 9000):
             diagnostico = "✅ Revisão de rotina recomendada (próximo da marca de 10.000 km)."
             checklist.extend([
                "Troca de óleo e filtro de óleo",
                "Alinhamento e balanceamento"
             ])

        # --- FIM DAS REGRAS ---

        # Remove itens duplicados da lista (caso regras diferentes adicionem o mesmo item)
        itens_finais = list(dict.fromkeys(checklist))

        # 3. Retornar o JSON completo
        return jsonify({
            "resultado": diagnostico,
            "itens_inspecao": itens_finais 
        })

    except Exception as e:
        return jsonify({"erro": str(e)}), 500


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)