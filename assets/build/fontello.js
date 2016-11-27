/* eslint-disable no-console */
'use strict'; // eslint-disable-line

const spawn = require('child_process').spawn;
const fs = require('fs');
const readline = require('readline');
const path = require('path');

const config = require('./config');

const CONFIG_PATH = 'assets/fonts/fontello.json';
const OUTPUT_PATH = 'assets/fonts/fontello';
const SCSS_PATH = 'assets/fonts/fontello/fontello.scss';

function fetchFontelloFonts(configPath, outputPath, callback) {
  const command = `./node_modules/.bin/fontello-cli --config ${configPath} --font ${outputPath} --css ${outputPath}`;
  const childProcess = spawn('/bin/sh', ['-c', command], {
    cwd: config.paths.root,
    stdio: 'pipe',
    env: process.env,
  });

  childProcess.stderr.on('data', data => console.error(`fontello-cli: ${data}`));
  childProcess.on('close', (code) => {
    if (code !== 0) console.error(`fontello-cli exited with code ${code}`);
    console.info('fontello-cli generated fonts in %s', outputPath);
    callback();
  });
}

function getVariablesSCSS(charCodes) {
  const output = Object.keys(charCodes).map((className) => {
    const charCode = charCodes[className];
    return `$${className}: "${charCode}";`;
  });
  return output.join('\n');
}

fetchFontelloFonts(CONFIG_PATH, OUTPUT_PATH, () => {
  const charCodes = {};
  const codesCssPath = path.join(OUTPUT_PATH, 'fontawesome-codes.css');
  const lineReader = readline.createInterface({
    input: fs.createReadStream(codesCssPath),
  });

  lineReader.on('line', (line) => {
    const vars = line.match(/^\.fa-([^:]+):before { content: '([^']+)'; }/);
    if (vars) {
      const cssClass = vars[1];
      const charCode = vars[2];
      charCodes[`fa-var-${cssClass}`] = charCode;
    }
  });

  lineReader.on('close', () => {
    const scss = getVariablesSCSS(charCodes);
    fs.writeFileSync(SCSS_PATH, scss);
    console.info('generated scss file with font variables in %s', SCSS_PATH);
  });
});
