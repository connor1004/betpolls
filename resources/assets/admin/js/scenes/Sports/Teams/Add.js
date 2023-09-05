import React, { Component } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup';
import {
  Card,
  Form, FormLayout,
  InlineError, TextField, Button
} from '@shopify/polaris';

import FileInput from '../../../components/FileInput';

class Add extends Component {
  constructor(props) {
    super(props);
    this.formikRef = React.createRef();
    this.handleAdd = this.handleAdd.bind(this);
  }

  async handleAdd(values, bags) {
    const {
      onAdd
    } = this.props;
    onAdd(values, bags, this);
  }

  render() {
    return (
      <Formik
        ref={this.formikRef}
        initialValues={{
          ref_id: 0,
          logo: '',
          name: '',
          name_es: '',
          short_name: '',
          short_name_es: '',
          slug: '',
          slug_es: '',
          title: '',
          title_es: '',
          meta_keywords: '',
          meta_keywords_es: '',
          meta_description: '',
          meta_description_es: '',
          meta: {
            injured: ''
          },
          meta_es: {
            injured: ''
          }
        }}
        validationSchema={
          Yup.object().shape({
            ref_id: Yup.number('Ref id must be a number!').required('Ref id is required!'),
            logo: Yup.string().required('Logo is required!').url('Logo should be url'),
            name: Yup.string().required('Name is required!'),
            name_es: Yup.string().required('Name ES is required!'),
            short_name: Yup.string().required('Short name is required!'),
            short_name_es: Yup.string().required('Short name ES is required!')
          })
        }
        onSubmit={this.handleAdd}
        render={({
          values,
          errors,
          status,
          touched,
          setFieldValue,
          handleBlur,
          handleSubmit,
          isSubmitting
        }) => (
          <Card sectioned>
            <Form onSubmit={handleSubmit}>
              {status && <InlineError message={status} />}
              <FormLayout>
                <FormLayout.Group>
                  <TextField
                    type="number"
                    label="Ref id"
                    placeholder="Ref id"
                    name="ref_id"
                    value={values.ref_id}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('ref_id', value);
                    }}
                    error={touched.ref_id && errors.ref_id}
                  />
                  <FileInput
                    label="Logo Url"
                    name="logo"
                    placeholder="Logo URL"
                    value={values.logo}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('logo', value);
                    }}
                    error={touched.logo && errors.logo}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Name"
                    placeholder="Name"
                    name="name"
                    value={values.name}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('name', value);
                    }}
                    error={touched.name && errors.name}
                  />
                  <TextField
                    label="Name ES"
                    placeholder="Name ES"
                    name="name_es"
                    value={values.name_es}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('name_es', value);
                    }}
                    error={touched.name_es && errors.name_es}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Short name"
                    placeholder="Short name"
                    name="short_name"
                    value={values.short_name}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('short_name', value);
                    }}
                    error={touched.short_name && errors.short_name}
                  />
                  <TextField
                    label="Short name ES"
                    placeholder="Short name ES"
                    name="short_name_es"
                    value={values.short_name_es}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('short_name_es', value);
                    }}
                    error={touched.short_name_es && errors.short_name_es}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Slug"
                    placeholder="Slug"
                    name="slug"
                    value={values.slug}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('slug', value);
                    }}
                    error={touched.slug && errors.slug}
                  />
                  <TextField
                    label="Slug ES"
                    placeholder="Slug ES"
                    name="slug_es"
                    value={values.slug_es}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('slug_es', value);
                    }}
                    error={touched.slug_es && errors.slug_es}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Title"
                    placeholder="Title"
                    name="title"
                    value={values.title}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('title', value);
                    }}
                    error={touched.title && errors.title}
                  />
                  <TextField
                    label="Title ES"
                    placeholder="Title ES"
                    name="title_es"
                    value={values.title_es}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('title_es', value);
                    }}
                    error={touched.title_es && errors.title_es}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Meta keywords"
                    placeholder="Meta keywords"
                    name="meta_keywords"
                    value={values.meta_keywords}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_keywords', value);
                    }}
                    error={touched.meta_keywords && errors.meta_keywords}
                  />
                  <TextField
                    label="Meta keywords ES"
                    placeholder="Meta keywords ES"
                    name="meta_keywords_es"
                    value={values.meta_keywords_es}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_keywords_es', value);
                    }}
                    error={touched.meta_keywords_es && errors.meta_keywords_es}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Meta description"
                    placeholder="Meta description"
                    name="meta_description"
                    multiline
                    value={values.meta_description}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_description', value);
                    }}
                    error={touched.meta_description && errors.meta_description}
                  />
                  <TextField
                    label="Meta description ES"
                    placeholder="Meta description ES"
                    name="meta_description_es"
                    multiline
                    value={values.meta_description_es}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_description_es', value);
                    }}
                    error={touched.meta_description_es && errors.meta_description_es}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Injured"
                    placeholder="Injured"
                    name="meta.injured"
                    multiline
                    value={values.meta.injured}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta.injured', value);
                    }}
                  />
                  <TextField
                    label="Injured ES"
                    placeholder="Injured ES"
                    name="meta_es.injured"
                    multiline
                    value={values.meta_es.injured}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_es.injured', value);
                    }}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <Button
                    loading={isSubmitting}
                    submit
                    primary
                    icon="circlePlus"
                    >
                      Add New
                  </Button>
                </FormLayout.Group>
              </FormLayout>
            </Form>
          </Card>
        )}
      />
    );
  }
}

Add.defaultProps = {
  onAdd: () => {}
};

export default Add;
