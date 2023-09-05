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


class AppSettings extends Component {
  constructor(props) {
    super(props);
    this.formikRef = React.createRef();
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  async componentDidMount() {
    const {
      response, body
    } = await Api.get('admin/generals/options/settings');
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
      title: values.title,
      meta_keywords: values.meta_keywords,
      meta_description: values.meta_description,
      contacts: values.contacts,
      ads_txt: values.ads_txt,
      robots_txt: values.robots_txt,
      verify_tag: values.verify_tag,
      analytics_code: values.analytics_code
    };
  }

  paramsToFormValues(params) {
    return {
      title: params.title || '',
      meta_keywords: params.meta_keywords || '',
      meta_description: params.meta_description || '',
      contacts: params.contacts || '',
      ads_txt: params.ads_txt || '',
      robots_txt: params.robots_txt || '',
      verify_tag: params.verify_tag || '',
      analytics_code: params.analytics_code || ''
    };
  }

  async handleSubmit(values, bags) {
    const params = this.formValuesToParams(values);
    const {
      body, response
    } = await Api.put('admin/generals/options/settings', params);
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
      <Card title="Main Settings" sectioned>
        <Formik
          ref={this.formikRef}
          initialValues={this.paramsToFormValues({})}
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
                    label="ads.txt"
                    type="text"
                    value={values.ads_txt}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('ads_txt', value);
                    }}
                    error={touched.ads_txt && errors.ads_txt}
                    multiline
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Google site verification"
                    type="text"
                    value={values.verify_tag}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('verify_tag', value);
                    }}
                    error={touched.verify_tag && errors.verify_tag}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Google analytics code"
                    type="text"
                    value={values.analytics_code}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('analytics_code', value);
                    }}
                    error={touched.analytics_code && errors.analytics_code}
                    multiline
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Robots.txt"
                    type="text"
                    value={values.robots_txt}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('robots_txt', value);
                    }}
                    error={touched.robots_txt && errors.robots_txt}
                    multiline
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Title"
                    type="text"
                    value={values.title}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('title', value);
                    }}
                    error={touched.title && errors.title}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Meta keywords"
                    type="text"
                    value={values.meta_keywords}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_keywords', value);
                    }}
                    error={touched.meta_keywords && errors.meta_keywords}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Meta description"
                    type="text"
                    multiline
                    value={values.meta_description}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_description', value);
                    }}
                    error={touched.meta_description && errors.meta_description}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Contact emails"
                    type="text"
                    value={values.contacts}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('contacts', value);
                    }}
                    error={touched.contacts && errors.contacts}
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

export default AppSettings;
