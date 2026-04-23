export default {
    /**
     * Entfernt aus einem Syntax-String, z.B. "[[process.outputs.field_1((Prozess-Daten - field_1))]]"
     * oder "process|001900c6-443a-4fbe-a7d5-99b7697ae79f[Demo-Prozess]" das Label und gibt dafür ein Objekt mit "label" und "value" zurück.
     * "value" ist dann die Syntax ohne das Label.
     * @param syntax
     * @returns {{original: string, namespace: null, syntax: string, label: null, type: null, key: null}}
     */
    getSyntaxParts(syntax) {
        if (typeof syntax !== 'string') {
            syntax = '';
        }

        let parts = {
            original: syntax,
            label: null,
            syntax: syntax,
            namespace: null, // Nur bei PipeSyntax
            key: null, // Nur bei PipeSyntax
            type: null
        };

        // Prüfen ob der Wert ein Label hat, z.b. bei [[process.outputs.field_1((Prozess-Daten - field_1))]]
        if (syntax.startsWith('[[') && syntax.endsWith(']]')) {
            parts.type = 'syntax';

            if (syntax.includes('((')) {
                let match = syntax.match("\\(\\((.*?)\\)\\)");

                if (Array.isArray(match) && match.length > 1) {
                    parts.label = match[1];
                    parts.syntax = syntax.replaceAll(match[0], '');
                }
            }
        }

        // Prüfen, ob der Wert ein label hat, z.B. bei "process|d84dad32-55ed-4abc-91b7-3c5d7817d0da[Prozess Test 1]"
        else if (syntax.includes('|')) {
            parts.type = 'pipe';

            // --> Label ermitteln: "Prozess Test 1"
            if (syntax.includes('[')) {
                let label = syntax.split('[')[1].slice(0, -1);

                parts.label = label;
                parts.syntax = syntax.replaceAll('[' + label + ']', '');
                parts.key = parts.syntax.split('|')[1];

                if (syntax.includes('::')) {
                    parts.namespace = syntax.split('::')[0];
                }
            }
        }

        return parts;
    },

    /**
     * Gibt das Label einer Syntax zurück.
     * @param syntax
     * @param label
     * @returns {*|string}
     */
    setSyntaxLabel(syntax, label) {
        let parts = this.getSyntaxParts(syntax);

        // Syntax ohne Label
        let added = parts.original;

        if (!label) {
            return syntax;
        }

        // [[]]-Syntax
        if (parts.type === 'syntax') {
            return added.substring(0, added.length - 2) + '((' + label + '))]]';
        }
        if (parts.type === 'pipe') {
            return added + '[' + label + ']';
        }

        return syntax;
    }
}
