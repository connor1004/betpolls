/* eslint-disable no-param-reassign */
/* eslint-disable no-shadow */
import { createBrowserHistory } from 'history';
import { routerReducer, routerMiddleware } from 'react-router-redux';
import { createStore, combineReducers, applyMiddleware } from 'redux';
import thunkMiddleware from 'redux-thunk';
import { createLogger } from 'redux-logger';
import { composeWithDevTools } from 'redux-devtools-extension/developmentOnly';

import reducers from './reducers';
import fetchInterceptor from './interceptors/fetch';

// Add the reducer to your store on the `routing` key
export const history = createBrowserHistory();

const historyRouterMiddleware = routerMiddleware(history);

const getMiddleware = () => {
  if (process.env.NODE_ENV === 'production') {
    return applyMiddleware(historyRouterMiddleware, thunkMiddleware);
  }
  // Enable additional logging in non-production environments.
  return applyMiddleware(historyRouterMiddleware, thunkMiddleware, createLogger());
};

export const store = createStore(
  combineReducers({
    ...reducers,
    routing: routerReducer
  }),
  composeWithDevTools(
    fetchInterceptor(),
    getMiddleware()
  )
);
