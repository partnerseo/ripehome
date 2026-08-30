#!/usr/bin/env bash
# Iki motorun ayni girdide ayni sonucu verdigini dogrular.
# CI'da bu betik kirmizi olursa PHP ve TypeScript tarafi ayrismis demektir.
set -euo pipefail
here="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

php "$here/php/run-tests.php" --json > /tmp/ga-php.json
node --experimental-strip-types "$here/ts/run-tests.ts" --json 2>/dev/null > /tmp/ga-ts.json

python3 - "$@" <<'PY'
import json, sys

php = json.load(open('/tmp/ga-php.json'))
ts  = json.load(open('/tmp/ga-ts.json'))

def norm(o):
    if isinstance(o, dict):  return {k: norm(v) for k, v in o.items()}
    if isinstance(o, list):  return [norm(v) for v in o]
    if isinstance(o, float) and o.is_integer(): return int(o)
    return o

php, ts = norm(php), norm(ts)
diffs = []

for group in ('vectors', 'behaviour'):
    keys = set(php[group]) | set(ts[group])
    for name in sorted(keys):
        a, b = php[group].get(name), ts[group].get(name)
        if a is None or b is None:
            diffs.append(f'{group}/{name}: yalnizca birinde var')
            continue
        for field in sorted(set(a) | set(b)):
            if a.get(field) != b.get(field):
                diffs.append(f'{group}/{name}.{field}: PHP={a.get(field)!r} TS={b.get(field)!r}')

checks = sum(len(php[g]) for g in ('vectors', 'behaviour'))
fields = sum(len(v) for g in ('vectors', 'behaviour') for v in php[g].values())

if diffs:
    print(f'AYRISMA — {len(diffs)} fark:\n')
    print('\n'.join(diffs))
    sys.exit(1)

print(f'PHP ve TypeScript motorlari ayni: {checks} senaryo, {fields} alan, sifir fark.')
PY
