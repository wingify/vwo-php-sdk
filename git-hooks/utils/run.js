const childProcess = require('child_process');
const AnsiColorEnum = require('../enums/AnsiColorEnum');

function _exec(cmd) {
  return childProcess
    .execSync(cmd, {stdio: 'inherit'})
}

function run({
  name = 'Unknwon',
  command = `echo 'No command was specified!'`,
  successMessage = 'Passed',
  failureMessage = 'Failed'
}) {
  console.log(`${AnsiColorEnum.CYAN}\nRunning ${name}${AnsiColorEnum.RESET} : ${AnsiColorEnum.YELLOW}${command}${AnsiColorEnum.RESET}\n`);
  try {
    _exec(command);
    console.log(`${AnsiColorEnum.GREEN}\n\n${name} ${successMessage}${AnsiColorEnum.RESET}\n\n`);
  } catch (e) {
    console.log(`${AnsiColorEnum.RED}\n\n${name} ${failureMessage}${AnsiColorEnum.RESET}\n\n`, e);
    process.exit(1);
  }
}

module.exports = {
  run
};
