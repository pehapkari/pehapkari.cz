// see https://github.com/symfony/demo/blob/2a330a3bbb827e145b6a49a3665b3b45a4235dee/assets/js/highlight.js

import hljs from 'highlight.js/lib/highlight';

import php from 'highlight.js/lib/languages/php';
hljs.registerLanguage('php', php);

import twig from 'highlight.js/lib/languages/twig';
hljs.registerLanguage('twig', twig);

import markdown from 'highlight.js/lib/languages/markdown';
hljs.registerLanguage('markdown', markdown);

import css from 'highlight.js/lib/languages/css';
hljs.registerLanguage('css', css);

import javascript from 'highlight.js/lib/languages/javascript';
hljs.registerLanguage('javascript', javascript);

import diff from 'highlight.js/lib/languages/diff';
hljs.registerLanguage('diff', diff);

import yaml from 'highlight.js/lib/languages/yaml';
hljs.registerLanguage('yaml', yaml);

// Where are those needed?
// clike?
// php-extras?

hljs.initHighlightingOnLoad();
