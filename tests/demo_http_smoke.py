"""Prueba HTTP con datos ficticios. Ejecutar solo contra una demo local desechable."""
import concurrent.futures
import json
import os
import time
import urllib.error
import urllib.parse
import urllib.request
import uuid

BASE = os.environ['BOOKSWAP_TEST_BASE_URL'].rstrip('/')
assert urllib.parse.urlparse(BASE).hostname in {'localhost', '127.0.0.1'}, 'Usar solo una demo local de prueba.'

def call(path, method='GET', body=None, expected=200):
    req = urllib.request.Request(BASE+'/api'+path, method=method, data=json.dumps(body).encode() if body is not None else None, headers={'Content-Type':'application/json'})
    try: response = urllib.request.urlopen(req, timeout=15)
    except urllib.error.HTTPError as error: response = error
    with response:
        raw = response.read().decode()
        assert response.status == expected, (path, response.status, raw)
        payload = json.loads(raw)
        assert payload['success'] == (expected < 400), payload
        return payload.get('data', {})

assert call('/health')['database'] == 'connected'
with urllib.request.urlopen(BASE+'/') as response:
    assert 'BookSwap' in response.read().decode()
tag = uuid.uuid4().hex[:10]
users=[]
for n in range(3):
    users.append(call('/users', 'POST', {'nombre':f'Lector Ficticio {n}', 'correo':f'prueba.{tag}.{n}@example.com', 'password':'ClaveSoloParaPruebas!'}, 201))
a,b,c=[u['id'] for u in users]
assert all('password' not in u for u in users)
call('/users','POST',{'nombre':'Duplicado','correo':f'prueba.{tag}.0@example.com','password':'Prueba'},409)
book=call('/books','POST',{'usuario_id':b,'titulo':'Libro Ficticio '+tag,'autor':'Autor de prueba'},201)
assert call('/books/search?q='+urllib.parse.quote(tag))['books']
invalid={'usuario_solicitante_id':a,'usuario_receptor_id':c,'libro_solicitado_id':book['id'],'mensaje':'Prueba'}
call('/exchanges','POST',invalid,422)
invalid['usuario_receptor_id']=b
exchange=call('/exchanges','POST',invalid,201)
e=exchange['id']
call('/messages','POST',{'solicitud_id':e,'usuario_emisor_id':c,'usuario_receptor_id':b,'mensaje':'Ajeno'},422)
msg=call('/messages','POST',{'solicitud_id':e,'usuario_emisor_id':a,'usuario_receptor_id':b,'mensaje':'Mensaje ficticio'},201)
assert any(m['id']==msg['id'] for m in call(f'/exchanges/{e}/messages')['messages'])
rating={'usuario_calificado_id':b,'usuario_calificador_id':a,'solicitud_id':e,'puntuacion':5,'comentario':'Comentario ficticio'}
call('/ratings','POST',rating,409)
def accept(_):
    req=urllib.request.Request(BASE+f'/api/exchanges/{e}/accept',method='PUT')
    try:
        with urllib.request.urlopen(req,timeout=15) as response:return response.status
    except urllib.error.HTTPError as error:return error.code
with concurrent.futures.ThreadPoolExecutor(max_workers=2) as pool:
    assert sorted(pool.map(accept,range(2)))==[200,409]
assert len([h for h in call('/history')['history'] if h['solicitud_id']==e])==1
assert call(f'/users/{a}/notifications')['notifications']
call('/ratings','POST',{**rating,'usuario_calificador_id':c},422)
call('/ratings','POST',{**rating,'puntuacion':6},422)
result=call('/ratings','POST',rating,201)
assert any(r['id']==result['id'] for r in call(f'/users/{b}/ratings')['ratings'])
call('/books/'+str(book['id']),'DELETE')
assert not call('/books/search?q='+urllib.parse.quote(tag))['books']
call('/exchanges','POST',invalid,409)
book2=call('/books','POST',{'usuario_id':b,'titulo':'Rechazo '+tag,'autor':'Autor de prueba'},201)
rejected=call('/exchanges','POST',{**invalid,'libro_solicitado_id':book2['id']},201)
assert call('/exchanges/'+str(rejected['id'])+'/reject','PUT',{})['estado']=='rechazada'
call('/ratings','POST',{**rating,'solicitud_id':rejected['id']},409)
call('/users','POST',{'nombre':'x'*40000},413)
print('PASS: flujo MySQL, origen web, datos ficticios, validaciones de participantes, aceptación concurrente, historial único y límite de petición.')
