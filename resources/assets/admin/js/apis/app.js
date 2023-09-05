import Cookie from 'js-cookie';

import { ENV } from '../configs';
import Rest from './rest';
import Filter from './filter';

const App = {
  getRequestUrl: url => (
    `${ENV.API_ENDPOINT}/${url}`
  ),
  getAuth: () => {
    const auth = localStorage.getItem('auth');
    try {
      if (auth) {
        return JSON.parse(auth);
      }
    } catch (e) {
      console.log(e);
    }
    return null;
  },
  setAuth: (auth) => {
    if (auth === null) {
      Cookie.remove('token');
      localStorage.removeItem('auth');
    } else {
      Cookie.set('token', auth.token, { expires: 365 });
    }
    localStorage.setItem('auth', JSON.stringify(auth));
  },
  get: async (url, params = {}, headers = {}) => {
    const realUrl = App.getRequestUrl(url);
    try {
      const res = await Rest.get(realUrl, params, headers);
      const json = await Filter.filter(headers, res);
      return json;
    } catch (error) {
      return null;
    }
  },
  post: async (url, params = {}, headers = {}) => {
    const realUrl = App.getRequestUrl(url);
    const res = await Rest.post(realUrl, params, headers);
    const data = await Filter.filter(headers, res);
    return data;
  },
  put: async (url, params = {}, headers = {}) => {
    const realUrl = App.getRequestUrl(url);
    try {
      const res = await Rest.put(realUrl, params, headers);
      const json = await Filter.filter(headers, res);
      return json;
    } catch (error) {
      return null;
    }
  },
  delete: async (url, params = {}, headers = {}) => {
    const realUrl = App.getRequestUrl(url);
    try {
      const res = await Rest.delete(realUrl, params, headers);
      const json = await Filter.filter(headers, res);
      return json;
    } catch (error) {
      return null;
    }
  }
};

export default App;
