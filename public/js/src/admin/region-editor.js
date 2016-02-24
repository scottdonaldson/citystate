import React from 'react';

class RegionEditor extends React.Component {

	constructor() {
		super();
		this.state = {};
	}

	update() {

	}

	render() {
		return (
			<form onSubmit={this.update}>
				<input className="button" type="submit" value="Update!" />
			</form>
		);
	}
}

export default RegionEditor;