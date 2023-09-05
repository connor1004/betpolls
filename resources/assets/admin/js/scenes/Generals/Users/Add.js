import React, { Component } from 'react';
import {
  Formik
} from 'formik';
import * as Yup from 'yup';
import Select from 'react-select';
import { withRouter } from 'react-router-dom';
import {
  Page,
  Card, Form, FormLayout, InlineError, Button,
  TextField, Checkbox,
  Stack
} from '@shopify/polaris';
import Api from '../../../apis/app';
import { COUNTRIES_OPTIONS } from '../../../configs/options';
import { USER_ROLE } from '../../../configs/enums';

class AddUser extends Component {
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
      ]
    };
    this.formikRef = React.createRef();
    this.handleGoBack = this.handleGoBack.bind(this);
    this.handleAddUser = this.handleAddUser.bind(this);
  }

  handleGoBack() {
    this.props.history.goBack();
  }

  async handleAddUser(values, bags) {
    bags.setStatus();
    const newData = {
      firstname: values.firstname,
      lastname: values.lastname,
      username: values.username,
      email: values.email,
      password: values.password,
      country: values.country.code,
      role: values.role.value,
      robot: values.robot
    };
    const {
      body, response
    } = await Api.post('admin/generals/users', newData);
    switch (response.status) {
      case 422:
        bags.setErrors(body);
        break;
      case 200:
        this.props.history.replace(`/admin/generals/users/${body.id}`);
        break;
      default:
        break;
    }
    await bags.setSubmitting(false);
  }

  render() {
    const { userRole } = this.state;
    return (
      <Page
        fullWidth
        breadcrumbs={[{ content: 'Users', onAction: this.handleGoBack }]}
        title="Add User"
      >
        <Card title="User Info" sectioned>
          <Formik
            ref={this.formikRef}
            initialValues={{
              firstname: '',
              lastname: '',
              username: '',
              email: '',
              password: '',
              country: null,
              role: null,
              robot: 0
            }}
            validationSchema={
              Yup.object().shape({
                firstname: Yup.string().required('First name is required!'),
                lastname: Yup.string().required('Last name is required!'),
                username: Yup.string().required('Username is required!'),
                email: Yup.string().email('Email is not valid!').required('Email is required!'),
                password: Yup.string().min(6, 'Password has to be longer than 6 characters!').required('Password is required!'),
                country: Yup.string().required('Country is required!'),
                role: Yup.string().required('Role is required!')
              })
            }
            onSubmit={this.handleAddUser}
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
                    <Stack.Item fill>
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
                    </Stack.Item>
                  </Stack>
                  <Select
                    placeholder="Country"
                    classNamePrefix="react-select"
                    indicatorSeparator={null}
                    options={COUNTRIES_OPTIONS}
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
                      icon="circlePlus"
                      >
                        Add User
                    </Button>
                  </Stack>
                </FormLayout>
              </Form>
            )}
          />
        </Card>
      </Page>
    );
  }
}

export default withRouter(AddUser);
