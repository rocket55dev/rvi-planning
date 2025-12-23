module.exports = (plop) => {
	plop.setGenerator('block', {
		description: 'Create a reusable block',
		prompts: [
			{
				type: 'input',
				name: 'name',
				message: 'What is your block name?',
			},
		],
		actions: [
			{
				type: 'add',
				path: 'blocks/{{dashCase name}}/js/{{dashCase name}}.js',
				templateFile: 'plop-templates/block/block.js.hbs',
			},
            {
                type: 'add',
                path: 'blocks/{{dashCase name}}/sass/{{dashCase name}}.scss',
                templateFile: 'plop-templates/block/block.scss.hbs',
            },
            {
                type: 'add',
                path: 'blocks/{{dashCase name}}/{{dashCase name}}.php',
                templateFile: 'plop-templates/block/block.php.hbs',
            },
            {
                type: 'add',
                path: 'blocks/{{dashCase name}}/block.json',
                templateFile: 'plop-templates/block/block.json.hbs',
            },
			{
				type: 'append',
				path: 'blocks/register-blocks.php',
				pattern: `/* PLOP_INJECT_EXPORT */`,
				templateFile: 'plop-templates/template-customblock.php.hbs',
			},
		],
	});
};
