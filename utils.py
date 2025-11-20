# utils.py
import pandas as pd
import numpy as np

def prever_manutencao(data):
    """
    Previsão de manutenção preventiva.
    Espera: {'quilometragem': 12000, 'idade': 3, 'modelo': 'Fox'}
    """
    km = data.get('quilometragem', 0)
    idade = data.get('idade', 0)

    resultados = {}
    resultados['oleo'] = "Troca em {} km".format(max(0, 15000 - km))
    resultados['freios'] = "Verificar em {} km".format(max(0, 20000 - km))
    resultados['pneus'] = "Troca em {} meses".format(max(0, 12 - idade))
    return resultados

def diagnostico_veiculo(data):
    """
    Diagnóstico baseado em sintomas.
    Espera: {'sintomas': 'Motor falhando ao ligar'}
    """
    sintomas = data.get('sintomas', '').lower()
    if 'motor falhando' in sintomas:
        return {'possiveis_causas': ['Bateria fraca', 'Velas de ignição', 'Sensor de combustível']}
    elif 'freio chiando' in sintomas:
        return {'possiveis_causas': ['Pastilhas gastas', 'Disco irregular']}
    else:
        return {'possiveis_causas': ['Levar para verificação']}

def calcular_alertas(data):
    """
    Gera alertas preventivos para o veículo.
    Espera: {'quilometragem': 12000, 'idade': 3}
    """
    km = data.get('quilometragem', 0)
    idade = data.get('idade', 0)
    alertas = []
    if km > 10000:
        alertas.append("Troca de óleo recomendada")
    if idade > 2:
        alertas.append("Verificação de suspensão")
    return {'alertas': alertas}
