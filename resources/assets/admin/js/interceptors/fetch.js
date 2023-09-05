/* eslint-disable no-param-reassign */
import { logout } from '../actions/common';
import { history } from '../store';

const fetchInterceptor = (fetchContext = global) => {
  const _fetch = fetchContext.fetch;

  return createStore => (reducer, initialState, enhancer) => {
    const store = createStore(reducer, initialState, enhancer);

    fetchContext.fetch = async (url, options) => {
      // options.headers = {
      //   ...(options.headers || {}),
      //   ...Api.getAuthHeader()
      // };
      const res = await _fetch(url, options);
      if (res.status === 400 || res.status === 401 || res.status === 403) {
        await logout()(store.dispatch);
        history.push('/admin/login');
      }
      return res;
    };

    return store;
  };
};

export default fetchInterceptor;
