import pandas as pd

# Ler histórico de veículos
df = pd.read_csv('data/historico_veiculos.csv')

# Mostrar primeiras linhas
print(df.head())

# Exemplo: filtrar veículos com quilometragem acima de 20000
veiculos_alerta = df[df['quilometragem'] > 20000]
print(veiculos_alerta)
