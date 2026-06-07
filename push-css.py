import urllib.request, urllib.error, base64, json, sys

user = 'farook.maricar@hotmail.com'
pwd  = 'sz8FAtOvv1wL3H82JxxLgQEo'
token = base64.b64encode(f'{user}:{pwd}'.encode()).decode()
headers = {'Authorization': f'Basic {token}', 'Content-Type': 'application/json'}

css_path = r'E:\CCWS\PROJECTS\BHRC\site-build\bhrc-design-fix.css'
with open(css_path, 'r', encoding='utf-8') as f:
    css = f.read()

def req(method, url, data=None):
    body = json.dumps(data).encode() if data else None
    r = urllib.request.Request(url, data=body, headers=headers, method=method)
    try:
        resp = urllib.request.urlopen(r, timeout=25)
        return json.loads(resp.read())
    except urllib.error.HTTPError as e:
        return {'error': e.code, 'body': e.read().decode()[:400]}
    except Exception as e:
        return {'error': str(e)}

payload = {'styles': {'css': css}}

for gid in [6, 25]:
    result = req('PUT', f'https://behappyretired.com/wp-json/wp/v2/global-styles/{gid}', payload)
    if 'error' not in result:
        css_back = result.get('styles', {}).get('css', '')
        print(f'ID {gid}: SUCCESS — {len(css_back)} chars of CSS now live')
        sys.exit(0)
    else:
        print(f'ID {gid}: failed ({result["error"]}) — {result.get("body","")[:150]}')

print('Both IDs failed — manual deploy needed')
