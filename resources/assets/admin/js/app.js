import React, { Component } from 'react';
import { Router, Route, Switch } from 'react-router-dom';
import {
  AppProvider
} from '@shopify/polaris';

import {
  AppTheme
} from './themes';

import { history } from './store';
import Login from './scenes/Login';
import Main from './scenes/Main';
import PrivateRoute from './components/PrivateRoute';

class App extends Component {
  render() {
    return (
      <AppProvider theme={AppTheme}>
        <Router history={history}>
          <Switch>
            <Route path="/admin/login" name="Login" component={Login} />
            <PrivateRoute path="/admin" name="Main" component={Main} />
          </Switch>
        </Router>
      </AppProvider>
    );
  }
}

export default App;
