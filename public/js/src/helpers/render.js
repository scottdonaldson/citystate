import $ from 'jquery';
import ReactDOM from 'react-dom';

function render(component, id) {
	if ( $('#' + id).length ) {
		console.log('Found container with id:', id);
		console.log('Rendering component:', component.type.name);
		ReactDOM.render(component, document.getElementById(id));
	}
}

export default render;