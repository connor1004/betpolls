import React, { Component } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup';
import {
  Card,
  Form, FormLayout,
  InlineError, TextStyle,
  TextField, Button, Checkbox
} from '@shopify/polaris';
import CKEditor from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

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
          name: '',
          name_es: '',
          slug: '',
          slug_es: '',
          title: '',
          title_es: '',
          meta_keywords: '',
          meta_keywords_es: '',
          meta_description: '',
          meta_description_es: '',
          content: '',
          content_es: ''
        }}
        validationSchema={
          Yup.object().shape({
            name: Yup.string().required('Name is required!'),
            name_es: Yup.string().required('Name ES is required!')
          })
        }
        onSubmit={this.handleAdd}
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
          <Card sectioned>
            <Form onSubmit={handleSubmit}>
              {status && <InlineError message={status} />}
              <FormLayout>
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
                    name="Title"
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
                    value={values.meta_description}
                    onBlur={handleBlur}
                    multiline
                    onChange={(value) => {
                      setFieldValue('meta_description', value);
                    }}
                    error={touched.meta_description && errors.meta_description}
                  />
                  <TextField
                    label="Meta description ES"
                    placeholder="Meta description ES"
                    name="meta_description_es"
                    value={values.meta_description_es}
                    onBlur={handleBlur}
                    multiline
                    onChange={(value) => {
                      setFieldValue('meta_description_es', value);
                    }}
                    error={touched.meta_description_es && errors.meta_description_es}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <div>
                    <TextStyle>Content</TextStyle>
                    <CKEditor
                      editor={ClassicEditor}
                      data={values.content}
                      config={{
                        heading: {
                          options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            {
                              model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1'
                            },
                            {
                              model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2'
                            },
                            {
                              model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3'
                            },
                            {
                              model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4'
                            },
                            {
                              model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5'
                            },
                            {
                              model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6'
                            }
                          ]
                        }
                      }}
                      onChange={(event, editor) => {
                        setFieldValue('content', editor.getData());
                      }}
                    />
                  </div>
                  <div>
                    <TextStyle>Content ES</TextStyle>
                    <CKEditor
                      editor={ClassicEditor}
                      data={values.content_es}
                      config={{
                        heading: {
                          options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            {
                              model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1'
                            },
                            {
                              model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2'
                            },
                            {
                              model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3'
                            },
                            {
                              model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4'
                            },
                            {
                              model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5'
                            },
                            {
                              model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6'
                            }
                          ]
                        }
                      }}
                      onChange={(event, editor) => {
                        setFieldValue('content_es', editor.getData());
                      }}
                    />
                  </div>
                </FormLayout.Group>
                <FormLayout.Group>
                  <Button
                    loading={isSubmitting}
                    submit
                    primary
                    icon="circlePlus"
                    >
                      Add
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
