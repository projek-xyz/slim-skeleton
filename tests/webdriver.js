exports.config = {

    // Specify Test Files
    specs: [
        './tests/specs/**/*.js'
    ],
    //
    // ============
    // Capabilities
    // ============
    // Define your capabilities here. WebdriverIO can run multiple capabilities at the same
    // time. Depending on the number of capabilities, WebdriverIO launches several test
    // sessions. Within your capabilities you can overwrite the spec and exclude options in
    // order to group specific specs to a specific capability.
    //
    // First, you can define how many instances should be started at the same time. Let's
    // say you have 3 different capabilities (Chrome, Firefox, and Safari) and you have
    // set maxInstances to 1; wdio will spawn 3 processes. Therefore, if you have 10 spec
    // files and you set maxInstances to 10, all spec files will get tested at the same time
    // and 30 processes will get spawned. The property handles how many capabilities
    // from the same test should run tests.
    //
    maxInstances: 10,
    //
    // If you have trouble getting all important capabilities together, check out the
    // Sauce Labs platform configurator - a great tool to configure your capabilities:
    // https://docs.saucelabs.com/reference/platforms-configurator
    //
    capabilities: [
        { browserName: 'chrome' }
        // { browserName: 'firefox' },
        // { browserName: 'internet explorer' },
        // { browserName: 'safari' },
        // { browserName: 'opera' },
        // { browserName: 'iPad' },
        // { browserName: 'iPhone' },
        // { browserName: 'android' }
    ],
    //
    // ===================
    // Test Configurations
    // ===================
    // Define all options that are relevant for the WebdriverIO instance here
    //
    // By default WebdriverIO commands are executed in a synchronous way using
    // the wdio-sync package. If you still want to run your tests in an async way
    // log.g. using promises you can set the sync option to false.
    sync: true,
    //
    // Level of logging verbosity: silent | verbose | command | data | result | error
    logLevel: 'silent',
    //
    // Enables colors for log output.
    coloredLogs: true,
    //
    // Saves a screenshot to a given path if a command fails.
    screenshotPath: './tests/screens/',
    //
    // Default timeout for all waitFor* commands.
    waitforTimeout: 10000,
    //
    // Set a src URL in order to shorten url command calls. If your url parameter starts
    // with "/", the src url gets prepended.
    baseUrl: 'http://localhost:8000',
    //
    // Default timeout in milliseconds for request
    // if Selenium Grid doesn't send response
    connectionRetryTimeout: 90000,
    //
    // Default request retries count
    connectionRetryCount: 3,
    //
    // Initialize the browser instance with a WebdriverIO plugin. The object should have the
    // plugin name as key and the desired plugin options as properties. Make sure you have
    // the plugin installed before running any tests. The following plugins are currently
    // available:
    // WebdriverCSS: https://github.com/webdriverio/webdrivercss
    // WebdriverRTC: https://github.com/webdriverio/webdriverrtc
    // Browserevent: https://github.com/webdriverio/browserevent
    // plugins: {
    //     webdrivercss: {
    //         screenshotRoot: 'my-shots',
    //         failedComparisonsRoot: 'diffs',
    //         misMatchTolerance: 0.05,
    //         screenWidth: [320,480,640,1024]
    //     },
    //     webdriverrtc: {},
    //     browserevent: {}
    // },
    //
    // Test runner services
    // Services take over a specific job you don't want to take care of. They enhance
    // your test setup with almost no effort. Unlike plugins, they don't add new
    // commands. Instead, they hook themselves up into the test process.
    // services: ['browserstack'],
    //
    // Framework you want to run your specs with.
    // The following are supported: Mocha, Jasmine, and Cucumber
    // see also: http://webdriver.io/guide/testrunner/frameworks.html
    //
    // Make sure you have the wdio adapter package for the specific framework installed
    // before running any tests.
    framework: 'mocha',
    //
    mochaOpts: { ui: 'bdd' },
    //
    // =====
    // Hooks
    // =====
    // WebdriverIO provides several hooks you can use to interfere with the test process in order to enhance
    // it and to build services around it. You can either apply a single function or an array of
    // methods to it. If one of them returns with a promise, WebdriverIO will wait until that promise got
    // resolved to continue.
    //
    // Gets executed once before all workers get launched.
    // onPrepare: (config, capabilities) => {
    // },
    //
    // Gets executed before test execution begins. At this point you can access all global
    // variables, such as `browser`. It is the perfect place to define custom commands.
    before: (capabilities, specs) => {
        const chai = require('chai');
        global.expect = chai.expect;
        global.assert = chai.assert;

        chai.should();
    }
    //
    // Hook that gets executed before the suite starts
    // beforeSuite: (suite) => {
    // },
    //
    // Hook that gets executed _before_ a hook within the suite starts (log.g. runs before calling
    // beforeEach in Mocha)
    // beforeHook: () => {
    // },
    //
    // Hook that gets executed _after_ a hook within the suite starts (log.g. runs after calling
    // afterEach in Mocha)
    // afterHook: () => {
    // },
    //
    // Function to be executed before a test (in Mocha/Jasmine) or a step (in Cucumber) starts.
    // beforeTest: (test) => {
    // },
    //
    // Runs before a WebdriverIO command gets executed.
    // beforeCommand: (commandName, args) => {
    // },
    //
    // Runs after a WebdriverIO command gets executed
    // afterCommand: (commandName, args, result, error) => {
    // },
    //
    // Function to be executed after a test (in Mocha/Jasmine) or a step (in Cucumber) starts.
    // afterTest: (test) => {
    // },
    //
    // Hook that gets executed after the suite has ended
    // afterSuite: (suite) => {
    // },
    //
    // Gets executed after all tests are done. You still have access to all global variables from
    // the test.
    // after: (capabilities, specs) => {
    // },
    //
    // Gets executed after all workers got shut down and the process is about to exit. It is not
    // possible to defer the end of the process using a promise.
    // onComplete: function(exitCode) {
    // }
};
