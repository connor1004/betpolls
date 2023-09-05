import React, { Component } from 'react';
import {
  Card,
  Form,
  FormLayout,
  InlineError,
  Button,
  TextField
} from '@shopify/polaris';
import { Formik } from 'formik';
import * as Yup from 'yup';

import Api from '../../../apis/app';


class SocialMediaSettings extends Component {
  constructor(props) {
    super(props);
    this.formikRef = React.createRef();
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  async componentDidMount() {
    const {
      response, body
    } = await Api.get('admin/generals/options/social-medias');
    switch (response.status) {
      case 200:
        this.formikRef.current.setValues(this.paramsToFormValues(body));
        break;
      default:
        break;
    }
  }

  formValuesToParams(values) {
    return {
      facebook: values.facebook,
      twitter: values.twitter,
      instagram: values.instagram
    };
  }

  paramsToFormValues(params) {
    return {
      facebook: params.facebook || '',
      twitter: params.twitter || '',
      instagram: params.instagram || ''
    };
  }

  async handleSubmit(values, bags) {
    const params = this.formValuesToParams(values);
    const {
      body, response
    } = await Api.put('admin/generals/options/social-medias', params);
    switch (response.status) {
      case 200:
        bags.setValues(this.paramsToFormValues(body));
        break;
      default:
        break;
    }
    await bags.setSubmitting(false);
  }

  render() {
    return (
      <Card title="Social Media Settings" sectioned>
        <Formik
          ref={this.formikRef}
          initialValues={this.paramsToFormValues({})}
          validationSchema={
            Yup.object().shape({
              facebook: Yup.string().url('The value should be in URL format.'),
              twitter: Yup.string().url('The value should be in URL format.'),
              instagram: Yup.string().url('The value should be in URL format.'),
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
                <FormLayout.Group>
                  <TextField
                    label="Facebook"
                    type="text"
                    value={values.facebook}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('facebook', value);
                    }}
                    error={touched.facebook && errors.facebook}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Twitter"
                    type="text"
                    value={values.twitter}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('twitter', value);
                    }}
                    error={touched.twitter && errors.twitter}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Instagram"
                    type="text"
                    value={values.instagram}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('instagram', value);
                    }}
                    error={touched.instagram && errors.instagram}
                  />
                </FormLayout.Group>
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
    );
  }
}

export default SocialMediaSettings;
