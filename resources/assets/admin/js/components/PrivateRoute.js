import React from 'react';
import { Route, Redirect } from 'react-router-dom';
import Api from '../apis/app';

const PrivateRoute = ({ component: Component, ...rest }) => (
  <Route
    {...rest}
    render={props => (Api.getAuth()
      ? <Component {...props} />
      : <Redirect to={{ pathname: '/admin/login', state: { from: props.location } }} />)}
  />
);

export default PrivateRoute;
