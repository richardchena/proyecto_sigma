#pip install SQLAlchemy
#pip install nltk
#pip install advertools
#pip install openpyxl

#import nltk
#nltk.download('popular')

'''
CREATE TABLE IMA_WORD_FRECUENCY (
ID NUMERIC IDENTITY(1,1) NOT NULL PRIMARY KEY,
GRUPO_CLIENTE VARCHAR(50) NOT NULL,
SISTEMA VARCHAR(50) NOT NULL,
WORD VARCHAR(100) NOT NULL,
FRECUENCY NUMERIC NOT NULL,
FECHA DATE DEFAULT CAST(DATEADD(mm, DATEDIFF(m,0,GETDATE())-1,0) AS DATE))
'''

from pyodbc import connect
from pandas import read_sql, DataFrame, ExcelWriter
from sqlalchemy import create_engine

#print(drivers()) Verificar los drivers instalados

# Cadena de conexión para la base de datos
engine = create_engine(
    'mssql+pyodbc://{user}:{password}@{server}/{db}?{driver}'.format(
        user='richard',
        password='richard',
        server='LAPTOP-LKA9OM1E\SQLEXPRESS',
        db='PRUEBA',
        driver = 'driver=SQL+Server+Native+Client+11.0'
    )
)

# Query utilizado para traer la info a ser analizada de la base
query = "SELECT ANHO_MES, FECHA, GRUPO_CLIENTE, COMMENT FROM [PRUEBA].[DBO].[IMA_COMENTARIOS]"

'''
try:
    connection = engine.connect()
    df = read_sql(query, connection)
    array = df['COMMENT'].to_numpy()

    #print(array[0])

except Exception as ex:
    print(ex)

finally:
    connection.close()
    engine.dispose()

'''

from string import punctuation, digits
from numpy import array, append, delete, where

from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords
from nltk.probability import FreqDist
from nltk import Text
from advertools import stopwords as stw

spanish_stopword = list(stw['spanish']) # Función para ignorar palabras comunes en español
spanish_stopword.extend(['jajaja', 'jojojo']) # Lista de palabras para ignorar

datos = ['¡¿Hola mundo mundo como están? --yo me siento bien $además 09711554 xs', '¡¿Hola mundo como están? --yo me siento bien $pero 09711554 como como como']
text_tokens = array([])

def eliminar_caracteres_especiales(text):
    caracteres_especiales = punctuation + '\xa0\n\t¡¿«»' + digits
    return "".join([ch for ch in text if ch not in caracteres_especiales]).lower()

def eliminar_palabras(lista):
    for palabra in lista:
        if palabra in spanish_stopword:
            lista = delete(lista, where(lista == palabra))
    return lista

# Agregando las frases
for a in datos:
    text_tokens = append(text_tokens, word_tokenize(eliminar_caracteres_especiales(a)))

# Eliminado los stop words
text_tokens = eliminar_palabras(text_tokens)
    
# Obtener frecuencias de las palabras
texto_nltk = Text(text_tokens)
frequencias_nltk = FreqDist(texto_nltk).most_common(10) # Elegimos las principales palabras

df = DataFrame(frequencias_nltk, columns=['Word', 'Frecuency'])

with ExcelWriter(r'C:\Users\richa\OneDrive\Desktop\Richard\FPUNA\Python\TEXT\output.xlsx') as writer:
    df.to_excel(writer, index=False, sheet_name = 'SISTEMA1')
    df.to_excel(writer, index=False, sheet_name = 'SISTEMA2')
    df.to_excel(writer, index=False, sheet_name = 'SISTEMA3')

