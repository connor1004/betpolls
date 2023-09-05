import React, { Component } from 'react';
import {
  Card, Form, FormLayout, InlineError, Button,
  TextField, Checkbox,
  Stack
} from '@shopify/polaris';
import { Formik } from 'formik';
import * as Yup from 'yup';
import Select from 'react-select';

import { COUNTRIES_OPTIONS } from '../../../../configs/options';
import OptionsHelper from '../../../../helpers/OptionsHelper';
import { USER_ROLE } from '../../../../configs/enums';

class View extends Component {
  constructor(props) {
    super(props);
    this.state = {
      userRole: [
        {
          label: USER_ROLE.ADMIN, value: USER_ROLE.ADMIN
        },
        {
          label: USER_ROLE.VOTER, value: USER_ROLE.VOTER
        }
      ],
      countries: COUNTRIES_OPTIONS
    };
    this.formikRef = React.createRef();
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  componentWillReceiveProps(props) {
    if (this.props.data !== props.data) {
      this.formikRef.current.setValues(this.paramsToFormValues(props.data));
    }
  }

  formValuesToParams(values) {
    return {
      firstname: values.firstname,
      lastname: values.lastname,
      username: values.username,
      email: values.email,
      country: values.country.code,
      role: values.role.value,
      robot: values.robot
    };
  }

  paramsToFormValues(params) {
    return {
      firstname: params.firstname,
      lastname: params.lastname,
      username: params.username,
      email: params.email,
      country: OptionsHelper.getItem(params.country, 'code', this.state.countries),
      role: OptionsHelper.getOption(params.role, this.state.userRole),
      robot: params.robot
    };
  }

  async handleSubmit(values, bags) {
    const {
      onUpdate
    } = this.props;
    await onUpdate(this.formValuesToParams(values), bags, this);
  }

  render() {
    const {
      data
    } = this.props;
    const { userRole, countries } = this.state;
    return (
      <Card title="Details" sectioned>
        <Formik
          ref={this.formikRef}
          initialValues={this.paramsToFormValues(data)}
          validationSchema={
            Yup.object().shape({
              firstname: Yup.string().required('First name is required!'),
              lastname: Yup.string().required('Last name is required!'),
              username: Yup.string().required('Username is required!'),
              email: Yup.string().email('Email is not valid!').required('Email is required!'),
              country: Yup.string().required('Country is required!'),
              role: Yup.string().required('Role is required!')
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
                {status && <InlineError message={status} />}
                <Stack>
                  <Stack.Item fill>
                    <TextField
                      label="First Name"
                      type="text"
                      value={values.firstname}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('firstname', value);
                      }}
                      error={touched.firstname && errors.firstname}
                    />
                  </Stack.Item>
                  <Stack.Item fill>
                    <TextField
                      label="Last Name"
                      type="text"
                      value={values.lastname}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('lastname', value);
                      }}
                      error={touched.lastname && errors.lastname}
                    />
                  </Stack.Item>
                </Stack>
                <Stack>
                  <Stack.Item fill>
                    <TextField
                      label="Username"
                      type="text"
                      value={values.username}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('username', value);
                      }}
                      error={touched.username && errors.username}
                    />
                  </Stack.Item>
                  <Stack.Item fill>
                    <TextField
                      label="Email"
                      type="email"
                      value={values.email}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('email', value);
                      }}
                      error={touched.email && errors.email}
                    />
                  </Stack.Item>
                </Stack>
                <Select
                  placeholder="Country"
                  classNamePrefix="react-select"
                  indicatorSeparator={null}
                  options={countries}
                  getOptionValue={option => option.code}
                  getOptionLabel={option => option.name}
                  value={values.country}
                  onBlur={handleBlur}
                  onChange={async (value) => {
                    await setFieldValue('country', value);
                  }}
                />
                {(touched.country && !!errors.country) && <InlineError message={errors.country} />}
                <Stack>
                  <Stack.Item fill>
                    <Select
                      placeholder="Role"
                      classNamePrefix="react-select"
                      indicatorSeparator={null}
                      options={userRole}
                      getOptionValue={option => option.value}
                      getOptionLabel={option => option.label}
                      value={values.role}
                      onBlur={handleBlur}
                      onChange={async (value) => {
                        await setFieldValue('role', value);
                      }}
                    />
                    {(touched.role && !!errors.role) && <InlineError message={errors.role} />}
                  </Stack.Item>
                  <Stack.Item fill>
                    <Checkbox
                      label="Robot"
                      checked={values.robot === 1}
                      onChange={(value) => {
                        setFieldValue('robot', value === true ? 1 : 0);
                      }}
                    />
                  </Stack.Item>
                </Stack>
                <Stack>
                  <Stack.Item fill />
                  <Button
                    loading={isSubmitting}
                    submit
                    primary
                    >
                      Update
                  </Button>
                </Stack>
              </FormLayout>
            </Form>
          )}
        />
      </Card>
    );
  }
}

View.defaultProps = {
  data: {},
  onUpdate: () => {}
};

export default View;
