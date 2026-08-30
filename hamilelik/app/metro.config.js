const { getDefaultConfig } = require('expo/metro-config');
const path = require('node:path');

const projectRoot = __dirname;
// Hafta motoru uygulamanın dışında, api/ ile paylaşılan tek kaynakta durur.
// Metro varsayılan olarak proje kökünün dışına bakmaz; bu klasörü izlemesi
// gerekiyor, yoksa import çözülmez.
const engineRoot = path.resolve(projectRoot, '../engine/ts');

const config = getDefaultConfig(projectRoot);

config.watchFolders = [engineRoot];
config.resolver.nodeModulesPaths = [path.resolve(projectRoot, 'node_modules')];

module.exports = config;
