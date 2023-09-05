import React, { Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';

import {
  Page, Card,
  InlineError,
  Form, FormLayout,
  TextField, Button
} from '@shopify/polaris';
import { Formik } from 'formik';
import * as Yup from 'yup';

import Api from '../apis/app';
import { login } from '../actions/common';

class Login extends Component {
  constructor(props) {
    super(props);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.formikRef = React.createRef();
  }

  componentDidMount() {
    const auth = Api.getAuth();
    if (auth) {
      this.login(auth);
    }
  }

  async login(auth) {
    await this.props.login(auth);
    this.props.history.push('/admin');
  }

  async handleSubmit(values, bags) {
    const data = await Api.post('login', values);
    const { response, body } = data;
    switch (response.status) {
      case 422:
        bags.setErrors(body);
        console.log(body);
        break;
      case 406:
        bags.setStatus(body.error);
        break;
      case 200:
        this.login(body);
        break;
      default:
        break;
    }
    bags.setSubmitting(false);
  }

  render() {
    return (
      <Page
        singleColumn
      >
        <Card title="Login" sectioned>
          <Formik
            ref={this.formikRef}
            initialValues={{
              username: '',
              password: ''
            }}
            validationSchema={
              Yup.object().shape({
                username: Yup.string().required('Username is required!'),
                password: Yup.string().min(6, 'Password has to be longer than 6 characters!').required('Password is required!')
              })
            }
            onSubmit={this.handleSubmit}
            render={({
              values,
              errors,
              status,
              touched,
              setFieldValue,
              handleSubmit,
              handleBlur,
              isSubmitting
            }) => (
              <Form onSubmit={handleSubmit}>
                <FormLayout>
                  {status && <InlineError message={status} />}
                  <TextField
                    label="Username or Email"
                    value={values.username}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('username', value);
                    }}
                    error={touched.username && errors.username}
                  />

                  <TextField
                    label="Password"
                    type="password"
                    value={values.password}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('password', value);
                    }}
                    error={touched.password && errors.password}
                  />

                  <Button
                    loading={isSubmitting}
                    submit
                    primary
                    >
                      Login
                  </Button>
                </FormLayout>
              </Form>
            )}
          />
        </Card>
      </Page>
    );
  }
}

const mapStateToProps = () => ({});

const mapDispatchToProps = dispatch => ({
  login: bindActionCreators(login, dispatch)
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Login));
