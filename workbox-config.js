module.exports = {
	globDirectory: '.',
	globPatterns: [
		'**/*.{html,png,css,js,htm,gif,jpg}'
	],
	swDest: 'sw.js',
	ignoreURLParametersMatching: [
		/^utm_/,
		/^fbclid$/
	]
};