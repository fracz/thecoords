import findInFiles from 'find-in-files';
import yaml from 'js-yaml';
import fs from 'fs';
import chalk from 'chalk';
import ora from 'ora';

const regexes = [
    "\\$tc?\\([\"'](.+?)[\"']\\s*(,.+?)?[\\)\\{]", // $t('...'), $t('...', {}), $tc(...)
    "[\"'](.+?)[\"'].*//\\s*i18n", // '...' // i18n
    "\\s[^:\\s][a-z0-9-]+-i18n=\"(.+?)\"", // any-i18n="...", but not :any-i18n="..."
    "i18n:(\\[.+?\\])", // // i18n:['...']
    "<i18n path=\"(.+?)\"", // <i18n path="..."
];

const locations = [
    'src',
    '../backend/src',
];

let spinner = ora({text: 'Search for strings to translate in sources...', color: 'yellow'}).start();

async function readFiles() {
    const texts = [];
    for (const regex of regexes) {
        for (const location of locations) {
            await findInFiles.find(regex, location, '.(vue|js|php|html)$')
                .then(function (results) {
                    for (let result in results) {
                        results[result].matches.forEach(match => {
                            const text = match.match(regex);
                            if (regex == regexes[3]) {
                                const translations = eval(text[1]);
                                translations.forEach(t => texts.push(t.trim()));
                            } else {
                                texts.push(text[1].trim());
                            }
                        });
                    }
                });
        }
    }
    return texts
        .filter(elem => elem.indexOf('$t(') === -1)
        .filter((elem, pos, arr) => arr.indexOf(elem) == pos)
        .map(t => t.replace("\\'", "'"));
}

readFiles()
    .then(textsInSources => {
        spinner.succeed(chalk.green('Total strings to translate found: ' + textsInSources.length));
        console.log('');
        const translationsDirectory = 'src/i18n/lang';
        const yamlDumpConfig = {
            styles: {'!!null': 'canonical'},
            sortKeys: true,
            lineWidth: 1000
        };
        fs.readdirSync(translationsDirectory).forEach(file => {
            let translationFilePath = `${translationsDirectory}/${file}`;
            const existingMessages = yaml.load(fs.readFileSync(translationFilePath, 'utf8')) || {};
            const matched = {};
            let missing = {};

            for (let text of textsInSources) {
                if (existingMessages[text]) {
                    matched[text] = existingMessages[text];
                } else if (file !== 'en.yml' || text.indexOf('_') >= 0) {
                    missing[text] = null;
                } else if (text.indexOf('{') >= 0) {
                    matched[text] = text; // english dynamic strings need to be declared explicite
                }
                delete existingMessages[text];
            }

            for (const extraTranslation in existingMessages) {
                if (!existingMessages[extraTranslation]) {
                    delete existingMessages[extraTranslation];
                }
            }

            const matchedYml = yaml.dump(matched, yamlDumpConfig);
            const missingYml = yaml.dump(missing, yamlDumpConfig);
            const extraYml = yaml.dump(existingMessages, yamlDumpConfig);

            const matchedCount = Object.keys(matched).length;
            const extraCount = Object.keys(existingMessages).length;
            const missingCount = Object.keys(missing).length;

            const color = missingCount > 20 ? chalk.bgRed : (missingCount > 0 ? chalk.yellow : chalk.green);
            console.log(color(`${file}: correct: ${matchedCount}, extra: ${extraCount}, missing: ${missingCount}`));

            if (matchedCount + missingCount + extraCount === 0) {
                return;
            }

            fs.writeFileSync(translationFilePath, '# Do not add new translation keys manually. Run npm run collect-translations in order to update this file.\n#<editor-fold desc="Complete translations" defaultstate="collapsed">\n');
            if (matchedCount) {
                fs.appendFileSync(translationFilePath, matchedYml);
            }
            fs.appendFileSync(translationFilePath, '\n#</editor-fold>\n');
            fs.appendFileSync(translationFilePath, '#<editor-fold desc="Extra translations">\n# Translations below have not been found in sources. You might want to delete them.\n');
            if (extraCount) {
                fs.appendFileSync(translationFilePath, extraYml);
            }
            fs.appendFileSync(translationFilePath, '\n#</editor-fold>\n');
            fs.appendFileSync(translationFilePath, '#<editor-fold desc="Missing translations">\n# Translations below are missing. You want to translate them.\n');
            if (missingCount) {
                fs.appendFileSync(translationFilePath, missingYml);
            }
            fs.appendFileSync(translationFilePath, '\n#</editor-fold>\n');
        });
    });

