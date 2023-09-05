import React, { Component, Fragment } from 'react';
import {
  Card, Form, FormLayout, InlineError, Button, Thumbnail, ChoiceList, TextField
} from '@shopify/polaris';
import { Formik } from 'formik';

import Api from '../../../apis/app';
import FileInput from '../../../components/FileInput';

class BannerSettings extends Component {
  constructor(props) {
    super(props);
    this.formikRef = React.createRef();
    this.handleSubmit = this.handleSubmit.bind(this);
    this.bannerFields = [
      {
        main: 'top_main', small: 'top_small', title: 'Top Banner', type: 'top_type', ads: 'top_ads', link: 'top_link'
      },
      {
        main: 'middle_main', small: 'middle_small', title: 'Middle Banner', type: 'middle_type', ads: 'middle_ads', link: 'middle_link'
      },
      {
        main: 'bottom_main', small: 'bottom_small', title: 'Bottom Banner', type: 'bottom_type', ads: 'bottom_ads', link: 'bottom_link'
      },
      {
        main: 'game_main', small: 'game_small', title: 'Game Banner', type: 'game_type', ads: 'game_ads', link: 'game_link'
      },
      {
        main: 'side_main', small: 'side_small', title: 'Side Banner', type: 'side_type', ads: 'side_ads', link: 'side_link'
      }
    ];
  }

  async componentDidMount() {
    const {
      response, body
    } = await Api.get('admin/generals/options/banners');
    switch (response.status) {
      case 200:
        this.formikRef.current.setValues(this.paramsToFormValues(body));
        break;
      default:
        break;
    }
  }

  formValuesToParams(values) {
    const ret = {};
    for (let i = 0, ni = this.bannerFields.length; i < ni; i++) {
      const bannerField = this.bannerFields[i];
      ret[bannerField.type] = values[bannerField.type][0];
      ret[bannerField.ads] = values[bannerField.ads];
      ret[bannerField.main] = values[bannerField.main];
      ret[bannerField.small] = values[bannerField.small];
      ret[bannerField.link] = values[bannerField.link];
    }
    return ret;
  }

  paramsToFormValues(params) {
    const ret = {};
    for (let i = 0, ni = this.bannerFields.length; i < ni; i++) {
      const bannerField = this.bannerFields[i];
      ret[bannerField.type] = [(params && params[bannerField.type]) || 'none'];
      ret[bannerField.ads] = (params && params[bannerField.ads]) || '';
      ret[bannerField.main] = (params && params[bannerField.main]) || '';
      ret[bannerField.small] = (params && params[bannerField.small]) || '';
      ret[bannerField.link] = (params && params[bannerField.link]) || '';
    }
    return ret;
  }

  async handleSubmit(values, bags) {
    const params = this.formValuesToParams(values);
    const {
      body, response
    } = await Api.put('admin/generals/options/banners', params);
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
      <Card title="Banner Settings" sectioned>
        <Formik
          ref={this.formikRef}
          initialValues={this.paramsToFormValues({})}
          onSubmit={this.handleSubmit}
          render={({
            values,
            status,
            touched,
            errors,
            handleBlur,
            setFieldValue,
            handleSubmit,
            isSubmitting
          }) => (
            <Form onSubmit={handleSubmit}>
              <FormLayout>
                {status && <InlineError message={status} />}
                {this.bannerFields.map((field, index) => (
                  <div className="banner-part" key={`${index}`}>
                    <FormLayout.Group>
                      <ChoiceList
                        title={field.title}
                        choices={[
                          {label: 'Image', value: 'image'},
                          {label: 'Ads', value: 'ads'},
                          {label: 'None', value: 'none'},
                        ]}
                        selected={values[field.type]}
                        onChange={(value) => {
                          setFieldValue(field.type, value);
                        }}
                      />
                    </FormLayout.Group>
                    {values[field.type][0] == 'image' &&
                      <Fragment>
                        <FormLayout.Group>
                          <TextField
                            label="Image Action Url"
                            type="text"
                            value={values[field.link]}
                            onBlur={handleBlur}
                            onChange={(value) => {
                              setFieldValue(field.link, value);
                            }}
                            error={touched[field.link] && errors[field.link]}
                          />
                        </FormLayout.Group>
                        <FormLayout.Group>
                          <div className="banner">
                            <FileInput
                              label='Main'
                              name={field.main}
                              value={values[field.main]}
                              onBlur={handleBlur}
                              onChange={(value) => {
                                setFieldValue(field.main, value);
                              }}
                            />
                            <Thumbnail source={values[field.main]} />
                          </div>
                          <div className="banner">
                            <FileInput
                              label='Small'
                              name={field.small}
                              value={values[field.small]}
                              onBlur={handleBlur}
                              onChange={(value) => {
                                setFieldValue(field.small, value);
                              }}
                            />
                            <Thumbnail source={values[field.small]} />
                          </div>
                        </FormLayout.Group>
                      </Fragment>
                    }
                    {values[field.type][0] == 'ads' &&
                      <FormLayout.Group>
                        <TextField
                          label="Ads code"
                          type="text"
                          value={values[field.ads]}
                          onBlur={handleBlur}
                          onChange={(value) => {
                            setFieldValue(field.ads, value);
                          }}
                          error={touched[field.ads] && errors[field.ads]}
                          multiline
                        />
                      </FormLayout.Group>
                    }
                  </div>
                ))}
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

export default BannerSettings;
