/* eslint-disable no-unused-vars */
/* eslint-disable no-undef */
import 'babel-polyfill';
import 'dom4';
import 'whatwg-fetch';
import 'slick-carousel';
import 'select2';
import '@webcomponents/custom-elements/src/native-shim';
import '@webcomponents/custom-elements/src/custom-elements';
import 'lightpick';
import moment from 'moment';

import Navigation from './components/navigation';
import WeekSelector from './components/week-selector';
import LeagueGames from './components/league-games';
import Game from './components/game';
import EventGame from './components/event-game';
import FutureGame from './components/future-game';

import Common from './pages/common';
import Leagues from './pages/leagues';
import Leaderboard from './pages/leaderboard';
import Future from './pages/future';
import DateRangePicker from './components/daterange';

moment.locale('en');
window.customElements.define('app-navigation', Navigation);
window.customElements.define('app-week-selector', WeekSelector);
window.customElements.define('app-daterange-picker', DateRangePicker);
window.customElements.define('app-league-games', LeagueGames);

window.customElements.define('app-game', Game);
window.customElements.define('app-event-game', EventGame);
window.customElements.define('app-future-game', FutureGame);

const common = new Common();
const leagues = new Leagues();
const leaderboard = new Leaderboard();
const future = new Future();
