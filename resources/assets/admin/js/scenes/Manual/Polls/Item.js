import React, { Component } from 'react';
import {
  Card, Stack, Button, ButtonGroup, ResourceList, SkeletonBodyText, Thumbnail, TextStyle,
  Checkbox, Form, FormLayout, InlineError, Select, TextField, Icon
} from '@shopify/polaris';

import { Formik } from 'formik';
import * as Yup from 'yup';

import moment from 'moment-timezone';
import DatePicker from 'react-datepicker';
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
    this.formikRef = null;
    this.toggleEditMode = this.toggleEditMode.bind(this);
    this.handleEdit = this.handleEdit.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handleToggleActive = this.handleToggleActive.bind(this);
    this.handleDetail = this.handleDetail.bind(this);
    this.handleStatistics = this.handleStatistics.bind(this);
    this.toggleDeleteConfirm = this.toggleDeleteConfirm.bind(this);
    this.paramsToFormValues = this.paramsToFormValues.bind(this);
    this.formValuesToParams = this.formValuesToParams.bind(this);
  }

  setDeleting(isDeleting) {
    this.setState({
      isDeleting
    });
  }

  toggleDeleteConfirm() {
    this.setState({
      showDeleteConfirm: !this.state.showDeleteConfirm
    });
  }

  toggleEditMode() {
    this.setState({
      isEditMode: !this.state.isEditMode
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
  }

  async handleToggleActive() {
    const {
      onToggleActive, data
    } = this.props;
    await onToggleActive(data.id, this);
  }

  handleDetail() {
    const {
      data, onDetail
    } = this.props;
    onDetail(data);
  }

  handleStatistics() {
    const {
      data, onStatistics
    } = this.props;
    onStatistics(data);
  }

  handleToggleSelectItem(item) {
    this.props.OnToggleSelectItem(item);
  }

  paramsToFormValues(params) {
    var start_at = moment(moment.utc(params.start_at).toDate()).tz("America/New_York").format('YYYY-MM-DD HH:mm:ss');
    const values = {
      subcategory_id: (params.subcategory_id || '0') + '',
      name: params.name || '',
      name_es: params.name_es || '',
      slug: params.slug || '',
      slug_es: params.slug_es || '',
      start_at: moment(start_at).toDate(),
      logo: params.logo || '',
      location: params.location || '',
      location_es: params.location_es || '',
      is_future: params.is_future == 1 ? '1' : '0',
      status: params.status || 'not_started',
      show_scores: params.show_scores || 0,
      home_top_picks: params.home_top_picks || 0,
      published: params.published || 0,
      meta: {
        title: (params.meta && params.meta.title) || '',
        keywords: (params.meta && params.meta.keywords) || '',
        description: (params.meta && params.meta.description) || '',
        content: (params.meta && params.meta.content) || ''
      },
      meta_es: {
        title: (params.meta_es && params.meta_es.title) || '',
        keywords: (params.meta_es && params.meta_es.keywords) || '',
        description: (params.meta_es && params.meta_es.description) || '',
        content: (params.meta_es && params.meta_es.content) || ''
      }
    };
    return values;
  }

  formValuesToParams(values) {
    var start_at = moment(values.start_at).format('YYYY-MM-DD HH:mm:ss');
    start_at = moment.tz(start_at, "America/New_York");

    return {
      subcategory_id: values.subcategory_id,
      name: values.name,
      name_es: values.name_es,
      slug: values.slug,
      slug_es: values.slug_es,
      start_at: moment.utc(start_at).format('YYYY-MM-DD HH:mm:00'),
      logo: values.logo,
      location: values.location,
      location_es: values.location_es,
      is_future: parseInt(values.is_future) || 0,
      status: values.status,
      show_scores: values.show_scores,
      home_top_picks: values.home_top_picks,
      published: values.published,
      meta: {
        title: values.meta.title,
        keywords: values.meta.keywords,
        description: values.meta.description,
        content: values.meta.content
      },
      meta_es: {
        title: values.meta_es.title,
        keywords: values.meta_es.keywords,
        description: values.meta_es.description,
        content: values.meta_es.content
      }
    };
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

    const types = [
      {value: '1', label: 'Future'},
      {value: '0', label: 'Event'}
    ];

    const statuses = [
      {
        value: 'not_started',
        label: 'Not Started'
      },
      {
        value: 'started',
        label: 'Started'
      },
      {
        value: 'ended',
        label: 'Ended'
      }
    ];

    return (
      <Formik
        ref={this.formikRef}
        initialValues={this.paramsToFormValues(data)}
        validationSchema={
          Yup.object().shape({
            name: Yup.string().required('Name is required!'),
            name_es: Yup.string().required('Name ES is required!'),
            start_at: Yup.date().required('Start At is required!'),
          })
        }
        onSubmit={this.handleEdit}
        render={({
          values,
          errors,
          status,
          touched,
          handleBlur,
          setFieldValue,
          handleSubmit,
          isSubmitting
        }) => (
          <Card>
            <ResourceList.Item>
              <Form onSubmit={handleSubmit}>
                <FormLayout>
                  {status && <InlineError message={status} />}
                  <FormLayout.Group>
                    <Checkbox
                      label="Published"
                      checked={values.published == 1}
                      onChange={(value) => {
                        setFieldValue('published', value === true ? 1 : 0);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <Select
                      label="Subcategory"
                      placeholder="Subcategory"
                      options={this.props.subcategories}
                      value={values.subcategory_id}
                      onChange={(value) => {
                        setFieldValue('subcategory_id', value);
                      }}
                    />
                    <div className="full-width-input">
                      <label className="upper-label">
                        Start At
                      </label>
                      <DatePicker
                        selected={values.start_at}
                        showTimeSelect
                        dateFormat="MM/dd/YYYY hh:mm a"
                        timeIntervals={5}
                        showMonthDropdown
                        showYearDropdown
                        dropdownMode="select"
                        onChange={async (value) => {
                          await setFieldValue('start_at', value);
                        }}
                      />
                    </div>
                  </FormLayout.Group>
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
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Location"
                      placeholder="Location"
                      name="location"
                      value={values.location}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('location', value);
                      }}
                      error={touched.location && errors.location}
                    />
                    <TextField
                      label="Location ES"
                      placeholder="Location ES"
                      name="location_es"
                      value={values.location_es}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('location_es', value);
                      }}
                      error={touched.location_es && errors.location_es}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <Select
                      label="Type"
                      placeholder="Type"
                      options={types}
                      value={values.is_future}
                      onChange={async (value) => {
                        setFieldValue('is_future', value);
                      }}
                    />
                    <Select
                      label="Status"
                      placeholder="Status"
                      options={statuses}
                      value={values.status}
                      onChange={(value) => {
                        setFieldValue('status', value);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <Checkbox
                      label="Show Scores"
                      checked={values.show_scores == 1}
                      onChange={(value) => {
                        setFieldValue('show_scores', value === true ? 1 : 0);
                      }}
                    />
                    <Checkbox
                      label="Home Top Picks"
                      checked={values.home_top_picks == 1}
                      onChange={(value) => {
                        setFieldValue('home_top_picks', value === true ? 1 : 0);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Title"
                      placeholder="Title"
                      name="Title"
                      value={values.meta.title}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('meta.title', value);
                      }}
                    />
                    <TextField
                      label="Title ES"
                      placeholder="Title ES"
                      name="title_es"
                      value={values.meta_es.title}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('meta_es.title', value);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Meta keywords"
                      placeholder="Meta keywords"
                      name="meta_keywords"
                      value={values.meta.keywords}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('meta.keywords', value);
                      }}
                    />
                    <TextField
                      label="Meta keywords ES"
                      placeholder="Meta keywords ES"
                      name="meta_keywords_es"
                      value={values.meta_es.keywords}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('meta_es.keywords', value);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Meta description"
                      placeholder="Meta description"
                      name="meta_description"
                      value={values.meta.description}
                      onBlur={handleBlur}
                      multiline
                      onChange={(value) => {
                        setFieldValue('meta.description', value);
                      }}
                    />
                    <TextField
                      label="Meta description ES"
                      placeholder="Meta description ES"
                      name="meta_description_es"
                      value={values.meta_es.description}
                      onBlur={handleBlur}
                      multiline
                      onChange={(value) => {
                        setFieldValue('meta_es.description', value);
                      }}
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
    
    const statuses = {
      not_started: 'Not Started',
      started: 'Started',
      ended: 'Ended'
    };

    return (
      <div>
        <Card>
          <ResourceList.Item>
            {
              showDeleteConfirm ? (
                <Stack>
                  <Stack.Item fill>
                    Do you want to delete?
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
                    <div style={{ width: '40px' }}>
                      <Checkbox
                        checked={data.selected}
                        onChange={this.handleToggleSelectItem.bind(this, data)}
                      />
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '60px' }}>{data.id}</div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '60px' }}><Thumbnail source={data.logo} size="small" /></div>
                  </Stack.Item>
                  <Stack.Item>
                    <TextStyle>
                      <div style={{ width: '160px' }}>
                        {moment(moment.utc(data.start_at).toDate()).tz("America/New_York").format('DD/MM/YYYY hh:mm a')}
                      </div>
                    </TextStyle>
                  </Stack.Item>
                  <Stack.Item fill>
                    <div style={{ width: '350px' }}>{data.name}</div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '100px' }}>{data.is_future == 1 ? 'Future' : 'Event'}</div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '100px' }}>{data.subcategory ? data.subcategory.name : ''}</div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '100px' }}>{statuses[data.status]}</div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '100px' }}>
                      {data.published ? <Icon source='checkmark' /> : ''}
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '160px' }}>
                      <ButtonGroup segmented>
                        <Button
                          primary
                          icon="view"
                          onClick={this.handleDetail}
                        />
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
      </div>
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
  homeTeamFirst: false,
  isLoading: false,
  onToggleActive: () => { },
  onPull: () => { },
  onDelete: () => { },
  onDetail: () => { },
  onStatistics: () => { }
};

export default Item;
