import { extend } from 'flarum/extend';
import TextEditor from "flarum/components/TextEditor";
import AgendaButton from 'musiquesincongrues/flarum-ext-agenda/components/AgendaButton';

app.initializers.add('musiquesincongrues-flarum-ext-agenda', function() {
    extend(TextEditor.prototype, 'view', function (vdom) {
        vdom.children.splice(1, 0, m('div', ['RAOUL']));
    });
});
