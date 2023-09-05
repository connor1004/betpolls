import React, { Component } from 'react';
import {
  connect
} from 'react-redux';
import {
  withRouter
} from 'react-router-dom';
import {
  Card, Form, FormLayout, InlineError, Button,
  TextField,
  Thumbnail,
  Page,
  TextStyle
} from '@shopify/polaris';
import QueryString from 'qs';
import { Formik } from 'formik';
import * as Yup from 'yup';
import CKEditor from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

import { POST_TYPE_OPTIONS } from '../../../configs/options';
import OptionsHelper from '../../../helpers/OptionsHelper';
import FileInput from '../../../components/FileInput';
import Api from '../../../apis/app';
import { POST_TYPE } from '../../../configs/enums';

class Add extends Component {
  constructor(props) {
    super(props);
    this.state = {
      search: {}
    };
    this.formikRef = React.createRef();
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleGoBack = this.handleGoBack.bind(this);
  }

  async componentDidMount() {
    this.componentWillReceiveProps(this.props);
  }

  async componentWillReceiveProps(props) {
    const search = QueryString.parse(props.location.search, { ignoreQueryPrefix: true });
    await this.setState({
      search
    });
  }

  formValuesToParams(values) {
    return {
      featured_image: values.featured_image,
      title: values.title,
      title_es: values.title_es,
      slug: values.slug,
      slug_es: values.slug_es,
      content: values.content,
      content_es: values.content_es,
      excerpt: values.excerpt,
      excerpt_es: values.excerpt_es,
      meta_keywords: values.meta_keywords,
      meta_keywords_es: values.meta_keywords_es,
      meta_description: values.meta_description,
      meta_description_es: values.meta_description_es
    };
  }

  paramsToFormValues(params) {
    return {
      featured_image: params.featured_image,
      title: params.title,
      title_es: params.title_es,
      slug: params.slug,
      slug_es: params.slug_es,
      content: params.content,
      content_es: params.content_es,
      excerpt: params.excerpt,
      excerpt_es: params.excerpt_es,
      meta_keywords: params.meta_keywords,
      meta_keywords_es: params.meta_keywords_es,
      meta_description: params.meta_description,
      meta_description_es: params.meta_description_es
    };
  }

  async handleGoBack() {
    this.props.history.goBack();
  }

  async handleSubmit(values, bags) {
    const {
      search
    } = this.state;
    const params = this.formValuesToParams(values);
    params.post_type = search.post_type || POST_TYPE.POST;
    const {
      response, body
    } = await Api.post('admin/blogs/posts', params);
    switch (response.status) {
      case 422:
        bags.setErrors(body);
        break;
      case 200:
      default:
        this.props.history.push(`admin/blogs/posts/${body.id}${QueryString.stringify(search, { addQueryPrefix: true })}`, {
          data: body
        });
        break;
    }
    bags.setSubmitting(false);
  }

  render() {
    const {
      search
    } = this.state;
    const postOption = OptionsHelper.getOption(search.post_type, POST_TYPE_OPTIONS, POST_TYPE_OPTIONS[0]);
    return (
      <Page
        fullWidth
        breadcrumbs={[{ content: postOption.label, onAction: this.handleGoBack }]}
        title="Add New"
      >
        <Card sectioned>
          <Formik
            ref={this.formikRef}
            initialValues={this.paramsToFormValues({})}
            validationSchema={
              Yup.object().shape({
                title: Yup.string().required('title is required!'),
                meta_keywords: Yup.string().required('meta keywords is required!'),
                meta_description: Yup.string().required('meta description is required!')
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
                    <Thumbnail source={values.featured_image} size="large" />
                    <FileInput
                      label="Featured Image"
                      placeholder="Featured Image"
                      name="featured_image"
                      value={values.featured_image}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('featured_image', value);
                      }}
                      error={touched.featured_image && errors.featured_image}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      type="text"
                      label="Title"
                      placeholder="Title"
                      value={values.title}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('title', value);
                      }}
                      error={touched.title && errors.title}
                    />
                    <TextField
                      type="text"
                      label="Title ES"
                      placeholder="Title ES"
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
                      type="text"
                      label="Slug"
                      placeholder="Slug"
                      value={values.slug}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('slug', value);
                      }}
                      error={touched.slug && errors.slug}
                    />
                    <TextField
                      type="text"
                      label="Slug ES"
                      placeholder="Slug ES"
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
                      label="Excerpt"
                      placeholder="Excerpt"
                      multiline
                      value={values.excerpt}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('excerpt', value);
                      }}
                      error={touched.excerpt && errors.excerpt}
                    />
                    <TextField
                      label="Excerpt ES"
                      placeholder="Excerpt ES"
                      multiline
                      value={values.excerpt_es}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('excerpt_es', value);
                      }}
                      error={touched.excerpt_es && errors.excerpt_es}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      type="text"
                      label="Meta keywords"
                      placeholder="Meta keywords"
                      value={values.meta_keywords}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('meta_keywords', value);
                      }}
                      error={touched.meta_keywords && errors.meta_keywords}
                    />
                    <TextField
                      type="text"
                      label="Meta keywords ES"
                      placeholder="Meta keywords ES"
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
                      type="text"
                      label="Meta description"
                      placeholder="Meta description"
                      multiline
                      value={values.meta_description}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('meta_description', value);
                      }}
                      error={touched.meta_description && errors.meta_description}
                    />
                    <TextField
                      type="text"
                      label="Meta description ES"
                      placeholder="Meta description ES"
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
                          const data = editor.getData();
                          setFieldValue('content', data);
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
                          const data = editor.getData();
                          setFieldValue('content_es', data);
                        }}
                      />
                    </div>
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <Button
                      loading={isSubmitting}
                      submit
                      primary
                      >
                        Add New
                    </Button>
                  </FormLayout.Group>
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

const mapDispatchToProps = () /* dispatch */ => ({
  // logout: bindActionCreators(logout, dispatch)
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Add));
