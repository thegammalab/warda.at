const fs = require('fs');
const { resolve } = require('path');

const sampleConfigFile = resolve(__dirname, 'files/class-s3-smart-upload-configs.sample.php');
const destinationFile = resolve(__dirname, '../includes/class-s3-smart-upload-configs.php');

const argv = require('minimist')(process.argv.slice(2));
fs.readFile(sampleConfigFile, 'utf8', (err, data) => {
    if(err) {
        return console.log(err);
    }
    const result = data.replace(/<plugin_type>/g, argv.type, data);

    fs.writeFile(destinationFile, result, 'utf8', (err) => {
        if(err) {
            return console.log(err);
        }
        console.log('Build ok!');
    })
});