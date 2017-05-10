System.register('musiquesincongrues/flarum-ext-agenda/main', ['flarum/extend', 'flarum/components/TextEditor', 'musiquesincongrues/flarum-ext-agenda/components/AgendaButton'], function (_export) {
    'use strict';

    var extend, TextEditor, AgendaButton;
    return {
        setters: [function (_flarumExtend) {
            extend = _flarumExtend.extend;
        }, function (_flarumComponentsTextEditor) {
            TextEditor = _flarumComponentsTextEditor['default'];
        }, function (_musiquesincongruesFlarumExtAgendaComponentsAgendaButton) {
            AgendaButton = _musiquesincongruesFlarumExtAgendaComponentsAgendaButton['default'];
        }],
        execute: function () {

            app.initializers.add('musiquesincongrues-flarum-ext-agenda', function () {
                extend(TextEditor.prototype, 'view', function (vdom) {
                    vdom.children.splice(1, 0, m('div', ['RAOUL']));
                });
            });
        }
    };
});;
System.register('musiquesincongrues/flarum-ext-agenda/components/AgendaButton', ['flarum/Component'], function (_export) {
    'use strict';

    var Component, AgendaButton;
    return {
        setters: [function (_flarumComponent) {
            Component = _flarumComponent['default'];
        }],
        execute: function () {
            AgendaButton = (function (_Component) {
                babelHelpers.inherits(AgendaButton, _Component);

                function AgendaButton() {
                    babelHelpers.classCallCheck(this, AgendaButton);
                    babelHelpers.get(Object.getPrototypeOf(AgendaButton.prototype), 'constructor', this).apply(this, arguments);
                }

                babelHelpers.createClass(AgendaButton, [{
                    key: 'view',
                    value: function view() {
                        return m('div', [m('p', ["C'est un événement"])]);
                    }
                }]);
                return AgendaButton;
            })(Component);

            _export('default', AgendaButton);
        }
    };
});