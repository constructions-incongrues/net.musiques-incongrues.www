import Component from "flarum/Component";

export default class AgendaButton extends Component {

    view() {
        return m('div', [
            m('p', ["C'est un événement"]),
        ]);
    }

}
