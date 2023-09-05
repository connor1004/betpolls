import React, { Component } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup';
import {
  Card, Checkbox, Autocomplete,
  Form, FormLayout,
  InlineError,
  TextField, Button, ButtonGroup
} from '@shopify/polaris';
import Api from '../../../../../apis/app';
import OptionsHelper from '../../../../../helpers/OptionsHelper';

class AnswerAdd extends Component {
  constructor(props) {
    super(props);
    this.state = {
      candidates: [],
      options: [],
      selectedOptions: []
    };
    this.formikRef = React.createRef();
    this.handleAdd = this.handleAdd.bind(this);
  }

  async componentDidMount() {
    const {
      body: candidates
    } = await Api.get('admin/manual/candidates/all', {category_id: this.props.page.category.id});
    this.setState({
      candidates: OptionsHelper.getOptions(candidates, 'id', 'name')
    });
  }

  async handleAdd(values, bags) {
    const {
      onAdd
    } = this.props;
    onAdd(values, bags, this);
  }

  render() {
    const { candidates, options, selectedOptions } = this.state;
    return (
      <Formik
        ref={this.formikRef}
        initialValues={{
          name: '',
          score: 0,
          standing: '',
          odds: '',
          winning_points: '',
          losing_points: '',
          is_absent: 0
        }}
        validationSchema={
          Yup.object().shape({
            name: Yup.string().required('Name is required!'),
            score: Yup.number(),
            odds: Yup.number(),
            winning_points: Yup.number(),
            losing_points: Yup.number()
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
                  <Autocomplete
                    options={options}
                    selected={selectedOptions}
                    onSelect={(selected) => {
                      const selectedValue = selected.map((selectedItem) => {
                        const matchedOption = options.find((option) => {
                          return option.value.match(selectedItem);
                        });
                        return matchedOption && matchedOption.label;
                      });
                      this.setState({
                        selectedOptions: selected
                      });
                      
                      setFieldValue('name', selectedValue);
                    }}
                    textField={
                      <Autocomplete.TextField
                        onChange={(value) => {
                          setFieldValue('name', value)

                          if (value === '') {
                            this.setState({
                              options: candidates
                            });
                            return;
                          }

                          const filterRegex = new RegExp(value, 'i');
                          const resultOptions = candidates.filter((option) =>
                            option.label.match(filterRegex),
                          );
                          this.setState({
                            options: resultOptions
                          })
                        }}
                        label="Name"
                        value={values.name}
                        onBlur={handleBlur}
                        name="name"
                        error={touched.name && errors.name}
                      />
                    }
                  />
                  <TextField
                    label="Score"
                    placeholder="Score"
                    name="score"
                    value={values.score}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('score', value);
                    }}
                    error={touched.score && errors.score}
                  />
                  <TextField
                    label="Standing"
                    placeholder="Standing"
                    name="standing"
                    value={values.standing}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('standing', value);
                    }}
                    error={touched.standing && errors.standing}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Odds"
                    placeholder="Odds"
                    name="odds"
                    value={values.odds}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('odds', value);
                    }}
                    error={touched.odds && errors.odds}
                  />
                  <TextField
                    label="Winning Points"
                    placeholder="Winning Points"
                    name="winning_points"
                    value={values.winning_points}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('winning_points', value);
                    }}
                    error={touched.winning_points && errors.winning_points}
                  />
                  <TextField
                    label="Losing Points"
                    placeholder="Losing Points"
                    name="losing_points"
                    value={values.losing_points}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('losing_points', value);
                    }}
                    error={touched.losing_points && errors.losing_points}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <Checkbox
                    label="Absent"
                    checked={values.is_absent === 1}
                    onChange={(value) => {
                      setFieldValue('is_absent', value === true ? 1 : 0);
                    }}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <ButtonGroup segmented>
                    <Button
                      loading={isSubmitting}
                      submit
                      primary
                      icon="circlePlus"
                      >
                        Add
                    </Button>
                    <Button
                      icon="cancelSmall"
                      onClick={this.props.onClose}
                      >
                        Close
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

AnswerAdd.defaultProps = {
  onAdd: () => {},
  onClose: () => {}
};

export default AnswerAdd;
