import React, { Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import {
  Page, Card, Form, FormLayout, InlineError, Button,
  TextField, Banner
} from '@shopify/polaris';
import { Formik } from 'formik';
import * as Yup from 'yup';

import Api from '../apis/app';
import { login } from '../actions/common';

class ChangePassword extends Component {
  constructor(props) {
    super(props);
    this.formikRef = React.createRef();
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  async componentDidMount() {
    this.formikRef.current.setValues(this.paramsToFormValues({}));
  }

  formValuesToParams(values) {
    return {
      password: values.password,
      new_password: values.new_password,
      confirm_password: values.confirm_password,
    };
  }

  paramsToFormValues(params) {
    return {
      password: params.password || '',
      new_password: params.new_password || '',
      confirm_password: params.confirm_password || ''
    };
  }

  async login(auth) {
    await this.props.login(auth);
    this.props.history.push('/admin');
  }

  async handleSubmit(values, bags) {
    const params = this.formValuesToParams(values);
    params.user_id = this.props.auth.user.id;
    const {
      body, response
    } = await Api.put('admin/change-password', params);
    switch (response.status) {
      case 422:
        bags.setErrors(body);
        console.log(body);
        break;
      case 406:
        bags.setStatus({error: body.error});
        break;
      case 200:
        bags.setValues(this.paramsToFormValues({}));
        Api.setAuth(body);
        bags.resetForm();
        bags.setStatus({success: 'Password changed successfully!'})
        break;
      default:
        break;
    }
    await bags.setSubmitting(false);
  }

  render() {
    return (
      <Page
        singleColumn
      >
        <Card title="Change password" sectioned>
          <Formik
            ref={this.formikRef}
            initialValues={this.paramsToFormValues({})}
            validationSchema={
              Yup.object().shape({
                password: Yup.string().required('Old password is required!'),
                new_password: Yup.string().min(6, 'Password has to be longer than 6 characters!').required('New password is required!'),
                confirm_password: Yup.string().oneOf([Yup.ref('new_password'), null], 'Passwords must match').required('Confirm password is required!'),
              })
            }
            onSubmit={this.handleSubmit}
            render={({
              values,
              errors,
              status,
              handleBlur,
              touched,
              setFieldValue,
              handleSubmit,
              isSubmitting
            }) => (
              <Form onSubmit={handleSubmit}>
                <FormLayout>
                  {status && status.error && <InlineError message={status.error} />}
                  {status && status.success && !touched.password && !touched.new_password
                    && !touched.confirm_password
                    && <Banner title={status.success} status='success' />}
                  <TextField
                    label="Old password"
                    type="password"
                    value={values.password}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('password', value);
                    }}
                    error={touched.password && errors.password}
                  />
                  <TextField
                    label="New password"
                    type="password"
                    value={values.new_password}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('new_password', value);
                    }}
                    error={touched.new_password && errors.new_password}
                  />
                  <TextField
                    label="Confirm password"
                    type="password"
                    value={values.confirm_password}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('confirm_password', value);
                    }}
                    error={touched.confirm_password && errors.confirm_password}
                  />
                  <Button
                    loading={isSubmitting}
                    submit
                    primary
                    >
                      Update
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


const mapStateToProps = state => ({
  auth: state.common.auth
});

const mapDispatchToProps = dispatch => ({
  login: bindActionCreators(login, dispatch)
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(ChangePassword));