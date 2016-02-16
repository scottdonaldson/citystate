function json(res) {
	return res.json.bind(res);
}

module.exports = json;