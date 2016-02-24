var $ = require('jquery'),
	body = $('body');

import React from 'react';
import render from './helpers/render';
import RegionEditor from './admin/region-editor';

render(<RegionEditor />, 'region-editor');

/* 
body.on('click', '#alert', function() {
	$(this).remove();
});

body.on('click', '.region rect', function() {
	var id = $(this).closest('.region').attr('data-id');
	window.location.href = '/region/' + id;
});

var editing = false;

var editModule = $('<form class="module">');
var select = $('<select id="select-terrain">');
select.append('<option></option>');
select.append('<option value="grass">Grass</option>');
select.append('<option value="sand">Sand</option>');
$('#toolbar').append(select);

var submit = $('<button class="button">Submit</button>');
$('#toolbar').append(submit);

var tiles = [],
	selectedTile;

body.on('click', 'rect.shadow, .tile', function(e) {
	
	$('.selected').removeClass('selected');

	var $this = $(this).addClass('selected'),
		x = +$this.attr('x') / 60,
		y = +$this.attr('y') / 60;

	selectedTile = {
		x: x,
		y: y
	};

	tiles.push(selectedTile);
});

select.on('change', function() {

	if (selectedTile) selectedTile.type = this.value;

});

submit.on('click', function(e) {
	e.preventDefault();
	$.ajax({
		method: 'POST',
		url: '/api' + window.location.pathname,
		data: {
			tiles: tiles
		},
		success: function(data) {
			window.location.href = window.location.href;
		},
		error: function(err) {
			body.prepend('<div id="alert">' + err.message + '</div>');
		}
	})
})

*/