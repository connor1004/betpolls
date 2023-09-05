import React, { Component } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup';
import {
  Card, Form, FormLayout, InlineError,
  TextField, Button, ButtonGroup, Checkbox
} from '@shopify/polaris';
import Select from 'react-select';
import AsyncSelect from 'react-select/lib/Async';
import FileInput from '../../../components/FileInput';

import OptionsHelper from '../../../helpers/OptionsHelper';
import Api from '../../../apis/app';

class Edit extends Component {
  constructor(props) {
    super(props);
    this.formikRef = React.createRef();
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleReset = this.handleReset.bind(this);
    this.handleNew = this.handleNew.bind(this);

    this.handlePostInputChange = this.handlePostInputChange.bind(this);
    this.loadPostOptions = this.loadPostOptions.bind(this);
  }

  componentWillReceiveProps(props) {
    if (props.data) {
      const values = this.paramsToFormValues(props.data);
      this.formikRef.current.setValues(values);
      this.formikRef.current.setTouched({
        parent_id: false,
        top_menu: false,
        burger_menu: false,
        title: false,
        title_es: false,
        icon_url: false,
        url: false,
        url_es: false,
        category: false,
        league: false,
        post: false
      });
    }
  }

  async getPostOptions(inputValue) {
    const {
      body
    } = await Api.get('admin/blogs/posts/search', {
      name: inputValue
    });
    return body;
  }

  formValuesToParams(values) {
    return {
      parent_id: values.parent_id,
      top_menu: values.top_menu,
      burger_menu: values.burger_menu,
      title: values.title,
      title_es: values.title_es,
      icon_url: values.icon_url,
      url: values.url,
      url_es: values.url_es,
      ...(values.sport_category ? { sport_category_id: values.sport_category.id } : {}),
      ...(values.league ? { league_id: values.league.id } : {}),
      ...(values.post ? { post_id: values.post.id } : {})
    };
  }

  paramsToFormValues(params) {
    const {
      leagues, categories
    } = this.props;

    const values = {
      league: OptionsHelper.getItem(params.league_id, 'id', leagues),
      sport_category: OptionsHelper.getItem(params.sport_category_id, 'id', categories),
      post: params.post,
      parent_id: params.parent_id,
      top_menu: params.top_menu,
      burger_menu: params.burger_menu,
      url: params.url,
      url_es: params.url_es,
      icon_url: params.icon_url,
      title: params.title,
      title_es: params.title_es
    };
    return values;
  }

  async loadPostOptions(inputValue, callback) {
    callback(await this.getPostOptions(inputValue));
  }

  handlePostInputChange(value) {
    return value;
  }

  async handleSubmit(values, bags) {
    const {
      onSubmit
    } = this.props;
    onSubmit(this.formValuesToParams(values), bags);
  }

  handleReset() {
    this.formikRef.current.setValues({
      league: null,
      categories: null,
      parent_id: '',
      top_menu: false,
      burger_menu: false,
      url: '',
      title: ''
    });
  }

  handleNew() {
    const {
      onNew
    } = this.props;
    this.handleReset();
    onNew();
  }

  render() {
    const {
      leagues, categories, isEditing
    } = this.props;

    return (
      <Formik
        ref={this.formikRef}
        initialValues={{
          league: null,
          category: null,
          post: null,
          parent_id: '',
          top_menu: false,
          burger_menu: false,
          url: '',
          url_es: '',
          title: '',
          title_es: ''
        }}
        validationSchema={
          Yup.object().shape({
            url: Yup.string().required(),
            title: Yup.string().required()
          })
        }
        onSubmit={this.handleSubmit}
        render={({
          values,
          errors,
          status,
          touched,
          setValues,
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
                  {
                    !values.parent_id && (
                      <Checkbox
                        label="On top menu"
                        checked={values.top_menu === 1}
                        onChange={(value) => {
                          setFieldValue('top_menu', value === true ? 1 : 0);
                        }}
                      />
                    )
                  }
                  <Checkbox
                    label="Show menu"
                    checked={values.burger_menu === 1}
                    onChange={(value) => {
                      setFieldValue('burger_menu', value === true ? 1 : 0);
                    }}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <FileInput
                    label="Icon URL"
                    placeholder="Icon URL"
                    name="icon_url"
                    value={values.icon_url || ''}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('icon_url', value);
                    }}
                    error={touched.icon_url && errors.icon_url}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <Select
                    placeholder="League"
                    classNamePrefix="react-select"
                    indicatorSeparator={null}
                    getOptionLabel={option => (option.name)}
                    getOptionValue={option => (option.id)}
                    options={leagues}
                    value={values.league}
                    onBlur={handleBlur}
                    onChange={async (value) => {
                      await setValues({
                        league: value,
                        sport_category: null,
                        post: null,
                        url: value.url,
                        url_es: value.url_es,
                        title: value.title,
                        title_es: value.title_es
                      });
                    }}
                  />
                  {(touched.league && !!errors.league) && <InlineError message={errors.league} />}
                  <Select
                    placeholder="Categories"
                    classNamePrefix="react-select"
                    indicatorSeparator={null}
                    options={categories}
                    getOptionLabel={option => (option.name)}
                    getOptionValue={option => (option.id)}
                    value={values.sport_category}
                    onBlur={handleBlur}
                    onChange={async (value) => {
                      await setValues({
                        league: null,
                        sport_category: value,
                        post: null,
                        url: value.url,
                        url_es: value.url_es,
                        title: value.title,
                        title_es: value.title_es
                      });
                    }}
                  />
                  {(touched.sport_category && !!errors.sport_category) && <InlineError message={errors.sport_category} />}
                  <AsyncSelect
                    placeholder="Post"
                    classNamePrefix="react-select"
                    cacheOptions
                    loadOptions={this.loadPostOptions}
                    value={values.post}
                    getOptionLabel={(value => (value.title))}
                    getOptionValue={(value => (value.id))}
                    onInputChange={this.handlePostInputChange}
                    onBlur={handleBlur}
                    onChange={async (value) => {
                      await setValues({
                        league: null,
                        sport_category: null,
                        post: value,
                        url: value.url,
                        url_es: value.url_es,
                        title: value.title,
                        title_es: value.title_es
                      });
                    }}
                  />
                  {(touched.post && errors.post) && <InlineError message={errors.post} />}
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
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
                    disabled={!!(values.sport_category || values.league || values.post)}
                    placeholder="Url"
                    name="url"
                    value={values.url}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('url', value);
                    }}
                    error={touched.url && errors.url}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    placeholder="Title ES"
                    name="title_es"
                    value={values.title_es}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('title_es', value);
                    }}
                    error={touched.title_es && errors.title_es}
                  />
                  <TextField
                    disabled={!!(values.sport_category || values.league || values.post)}
                    placeholder="Url ES"
                    name="url_es"
                    value={values.url_es}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('url_es', value);
                    }}
                    error={touched.url_es && errors.url_es}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <ButtonGroup>
                    <Button
                      loading={isSubmitting}
                      submit
                      primary
                    >
                      {isEditing ? 'Update Menu' : 'Create Menu'}
                    </Button>
                    <Button
                      button
                      onClick={this.handleReset}
                      icon="circleCancel"
                    >
                      Reset
                    </Button>
                    <Button
                      disabled={!isEditing}
                      button
                      destructive
                      onClick={this.handleNew}
                      icon="circlePlus"
                    >
                      New
                    </Button>
                  </ButtonGroup>
                </FormLayout.Group>
              </FormLayout>
            </Form>
          </Card>
        )}
      />
    );
  }
}

Edit.defaultProps = {
  data: {},
  onSubmit: () => { },
  onNew: () => { }
};

export default Edit;
