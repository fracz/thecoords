import chalk from 'chalk';
import ora from 'ora';
import fs from 'fs-extra';
import {exec} from 'child_process';
import {readFile} from 'fs/promises';
import del from 'del';
import {dirname} from 'path';
import {fileURLToPath} from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));

const project = JSON.parse(await readFile(new URL('../package.json', import.meta.url)));

process.chdir(__dirname + '/../../');
console.log(process.cwd());

const version = process.env.RELEASE_VERSION || project.version;
const releasePackageName = process.env.RELEASE_FILENAME || `${project.name}-v${version}${process.env.NODE_ENV === 'development' ? '-dev' : ''}.tar.gz`;

console.log(`${project.name} v${version}`);

console.log('');
console.log("Preparing release package.");
console.log('');

function start() {
    clearVendorDirectory();
}

function clearVendorDirectory() {
    const spinner = ora({text: 'Cleaning vendor directory.', color: 'yellow'}).start();
    del('backend/vendor/**/.git')
        .then(() => {
            spinner.succeed('Vendor directory cleaned.');
            clearReleaseDirectory();
        })
        .catch((err) => {
            console.log(err);
            spinner.fail();
        });
}

function clearReleaseDirectory() {
    const spinner = ora({text: 'Deleting release directory.', color: 'yellow'}).start();
    fs.remove('release/', function (err) {
        if (err) {
            spinner.fail();
            console.error(err);
        } else {
            spinner.succeed('Release directory deleted.');
            copyToReleaseDirectory();
        }
    });
}

function copyToReleaseDirectory() {
    const spinner = ora({text: 'Copying application files.', color: 'yellow'}).start();
    const directories = [
        'backend/',
        'public/',
    ];
    directories.forEach(function (filename) {
        fs.mkdirsSync('release/' + filename);
        fs.copySync(filename, 'release/' + filename);
    });
    createRequiredDirectories();
    copySingleRequiredFiles();
    clearLocalConfigFiles();
    spinner.succeed('Application files copied.');
    createZipArchive();
}

function createRequiredDirectories() {
    [
        'var/cache',
        'var/config',
        'var/logs',
        'var/events',
        'var/system',
    ].forEach(function (dirname) {
        fs.mkdirsSync('release/' + dirname);
    });
}

function copySingleRequiredFiles() {
    fs.copySync('cli', 'release/cli');
    fs.copySync('var/config/config.sample.yml', 'release/var/config/config.sample.yml');
}

function clearLocalConfigFiles() {
    const pathsToDelete = [
        'release/**/.gitignore',
        'release/backend/.github',
        'release/backend/php*',
        'release/backend/tests',
        'release/**/*.md',
    ];
    del.sync(pathsToDelete);
}

function createZipArchive() {
    const spinner = ora({text: 'Creating release archive.', color: 'yellow'}).start();
    exec('tar -czf ' + releasePackageName + ' -C release .', function (err) {
        if (err) {
            spinner.fail();
            console.log(err);
        } else {
            spinner.succeed('Release archive created.');
            console.log('');
            console.log("Package: " + chalk.green(releasePackageName));
            const fileSizeInBytes = fs.statSync(releasePackageName).size;
            console.log('Size: ' + Math.round(fileSizeInBytes / 1024) + 'kB (' + Math.round(fileSizeInBytes * 10 / 1024 / 1024) / 10 + 'MB)');
        }
    });
}

start();
