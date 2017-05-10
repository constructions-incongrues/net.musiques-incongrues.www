var flarum = require('flarum-gulp');

flarum({
  modules: {
    'musiquesincongrues/flarum-ext-agenda': [
      'src/**/*.js'
    ]
  }
});
