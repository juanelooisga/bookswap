const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync(require('node:path').join(__dirname, '../public/assets/app.js'), 'utf8');
const context = vm.createContext({ URL, window: { location: { href: 'https://demo.example/frontend.html' } } });
vm.runInContext(source, context);
test('La API usa el origen de la demo', () => {
  assert.equal(vm.runInContext('API', context), 'https://demo.example/api');
});
test('El contenido de usuarios se muestra como texto', () => {
  const html = vm.runInContext(`renderLibros({data:{books:[{titulo:'<img src=x onerror=alert(1)>', autor:'A & B', id:1, usuario_id:2, disponible:1}]}})`, context);
  assert.ok(html.includes('&lt;img src=x onerror=alert(1)&gt;'));
  assert.ok(html.includes('A &amp; B'));
  assert.ok(!html.includes('<img'));
});
