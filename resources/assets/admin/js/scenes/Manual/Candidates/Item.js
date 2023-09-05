import React, { Component } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup';
import {
  Card, Stack,
  Form, FormLayout, Select, TextStyle,
  InlineError, TextField, Button, ButtonGroup,
  ResourceList, SkeletonBodyText, Thumbnail
} from '@shopify/polaris';
import CKEditor from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

import FileInput from '../../../components/FileInput';

class Item extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isDeleting: false,
      isEditMode: false,
      showDeleteConfirm: false
    };
    this.formikRef = React.createRef();
    this.handleEdit = this.handleEdit.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handleToggleActive = this.handleToggleActive.bind(this);
    this.toggleEditMode = this.toggleEditMode.bind(this);
    this.toggleDeleteConfirm = this.toggleDeleteConfirm.bind(this);
  }

  setDeleting(isDeleting) {
    this.setState({
      isDeleting
    });
  }

  toggleEditMode() {
    this.setState({
      isEditMode: !this.state.isEditMode
    });
  }

  toggleDeleteConfirm() {
    this.setState({
      showDeleteConfirm: !this.state.showDeleteConfirm
    });
  }

  async handleEdit(values, bags) {
    const {
      onEdit, data
    } = this.props;
    await onEdit(data.id, values, bags, this);
  }

  async handleDelete() {
    const {
      onDelete, data
    } = this.props;
    await onDelete(data.id, this);
    this.toggleDeleteConfirm();
  }

  async handleToggleActive() {
    const {
      onToggleActive, data
    } = this.props;
    await onToggleActive(data.id, this);
  }

  renderEditMode() {
    const {
      data
    } = this.props;
    const config = {
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
    };
    return (
      <Formik
        ref={this.formikRef}
        initialValues={{
          logo: data.logo || '',
          country_id: data.country_id + '' || '0',
          candidate_type_id: data.candidate_type_id + '' || '0',
          name: data.name || '',
          name_es: data.name_es || '',
          short_name: data.short_name || '',
          short_name_es: data.short_name_es || '',
          slug: data.slug || '',
          slug_es: data.slug_es || '',
          title: data.title || '',
          title_es: data.title_es || '',
          meta_keywords: data.meta_keywords || '',
          meta_keywords_es: data.meta_keywords_es || '',
          meta_description: data.meta_description || '',
          meta_description_es: data.meta_description_es || '',
          meta: {
            content: (data.meta && data.meta.content) || ''
          },
          meta_es: {
            content: (data.meta_es && data.meta_es.content) || ''
          }
        }}
        validationSchema={
          Yup.object().shape({
            name: Yup.string().required('Name is required!'),
            name_es: Yup.string().required('Name ES is required!'),
            short_name: Yup.string().required('Short name is required!'),
            short_name_es: Yup.string().required('Short name ES is required!')
          })
        }
        onSubmit={this.handleEdit}
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
          <Card>
            <ResourceList.Item>
              <Form onSubmit={handleSubmit}>
                <FormLayout>
                  {status && <InlineError message={status} />}
                  <FormLayout.Group>
                    <FileInput
                      name="logo"
                      label="Logo URL"
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
                    <Select
                      label="Candidate Types"
                      placeholder="Candidate Types"
                      options={this.props.candidateTypes}
                      value={values.candidate_type_id}
                      onChange={(value) => {
                        setFieldValue('candidate_type_id', value);
                      }}
                    />
                    <Select
                      label="Country"
                      placeholder="Country"
                      options={this.props.countries}
                      value={values.country_id}
                      onChange={(value) => {
                        setFieldValue('country_id', value);
                      }}
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
                      multiline
                      name="meta_description"
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
                      multiline
                      name="meta_description_es"
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
                        data={values.meta.content}
                        config={config}
                        onChange={(event, editor) => {
                          setFieldValue('meta.content', editor.getData());
                        }}
                      />
                    </div>
                    <div>
                      <TextStyle>Content ES</TextStyle>
                      <CKEditor
                        editor={ClassicEditor}
                        data={values.meta_es.content}
                        config={config}
                        onChange={(event, editor) => {
                          setFieldValue('meta_es.content', editor.getData());
                        }}
                      />
                    </div>
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <ButtonGroup segmented>
                      <Button
                        loading={isSubmitting}
                        submit
                        primary
                        icon="checkmark"
                      />
                      <Button
                        onClick={this.toggleEditMode}
                        icon="cancelSmall"
                      />
                    </ButtonGroup>
                  </FormLayout.Group>
                </FormLayout>
              </Form>
            </ResourceList.Item>
          </Card>
        )}
      />
    );
  }

  renderViewMode() {
    const {
      data
    } = this.props;
    const {
      showDeleteConfirm, isDeleting
    } = this.state;

    return (
      <Card>
        <ResourceList.Item>
          {
            showDeleteConfirm ? (
              <Stack>
                <Stack.Item fill>
                  {`Do you want to delete ${data.name}?`}
                </Stack.Item>
                <Stack.Item>
                  <ButtonGroup segmented>
                    <Button
                      primary
                      loading={isDeleting}
                      onClick={this.handleDelete}
                    >
                      Yes
                    </Button>
                    <Button
                      disabled={isDeleting}
                      onClick={this.toggleDeleteConfirm}
                    >
                      No
                    </Button>
                  </ButtonGroup>
                </Stack.Item>
              </Stack>
            ) : (
              <Stack>
                <Stack.Item>
                  <div style={{ width: '50px' }}>{data.id}</div>
                </Stack.Item>
                <Stack.Item>
                  <div style={{ width: '60px' }}><Thumbnail source={data.logo} size="small" /></div>
                </Stack.Item>
                <Stack.Item fill>
                  <div style={{ width: '200px' }}>{data.name}</div>
                </Stack.Item>
                <Stack.Item fill>
                  <div style={{ width: '200px' }}>{data.name_es}</div>
                </Stack.Item>
                <Stack.Item>
                  <div style={{ width: '150px' }}>{data.short_name}</div>
                </Stack.Item>
                <Stack.Item>
                  <div style={{ width: '150px' }}>{data.short_name_es}</div>
                </Stack.Item>
                <Stack.Item>
                  <div style={{ width: '150px' }}>{data.candidate_type ? data.candidate_type.name : ''}</div>
                </Stack.Item>
                <Stack.Item>
                  <div style={{ width: '120px' }}>
                    <ButtonGroup segmented>
                      <Button
                        primary
                        icon="products"
                        onClick={this.toggleEditMode}
                      />
                      <Button
                        icon={data.deleted_at === null ? 'cancelSmall' : 'checkmark'}
                        primary={data.deleted_at !== null}
                        destructive={data.deleted_at === null}
                        onClick={this.handleToggleActive}
                      />
                      <Button
                        destructive
                        icon="delete"
                        onClick={this.toggleDeleteConfirm}
                      />
                    </ButtonGroup>
                  </div>
                </Stack.Item>
              </Stack>
            )
          }
        </ResourceList.Item>
      </Card>
    );
  }

  render() {
    const {
      isLoading
    } = this.props;
    const {
      isEditMode
    } = this.state;
    if (isLoading) {
      return (
        <Card>
          <ResourceList.Item>
            <SkeletonBodyText />
          </ResourceList.Item>
        </Card>
      );
    }
    return isEditMode ? this.renderEditMode() : this.renderViewMode();
  }
}

Item.defaultProps = {
  isLoading: false,
  onEdit: () => {},
  onDelete: () => {},
  onToggleActive: () => {}
};

export default Item;
