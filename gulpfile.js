var elixir = require('laravel-elixir');

require('laravel-elixir-webpack-official');
require('laravel-elixir-vue-2');

elixir(function(mix) {
    mix
    	.webpack('./public/src/admin.js', './dist/admin.js')
    	.webpack('./public/src/login.js', './dist/login.js');
});